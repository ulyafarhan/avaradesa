import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/auth_repository.dart';
import '../../data/auth_local.dart';
import '../../domain/user_model.dart';

class AuthState {
  final bool isLoading;
  final bool isAuthenticated;
  final UserModel? user;
  final String? error;

  const AuthState({
    this.isLoading = true,
    this.isAuthenticated = false,
    this.user,
    this.error,
  });

  AuthState copyWith({
    bool? isLoading,
    bool? isAuthenticated,
    UserModel? user,
    String? error,
  }) {
    return AuthState(
      isLoading: isLoading ?? this.isLoading,
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      user: user ?? this.user,
      error: error,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repository;
  final AuthLocal _local;

  AuthNotifier(this._repository, this._local) : super(const AuthState()) {
    checkAuth();
  }

  Future<void> checkAuth() async {
    final hasSession = await _local.hasSession();
    if (!hasSession) {
      state = state.copyWith(isLoading: false);
      return;
    }
    final token = await _local.getToken();
    if (token != null) {
      final response = await _repository.getProfile();
      if (response != null) {
        state = state.copyWith(isLoading: false, isAuthenticated: true, user: UserModel.fromJson(response));
      } else {
        await _local.clearSession();
        state = state.copyWith(isLoading: false);
      }
    } else {
      state = state.copyWith(isLoading: false);
    }
  }

  Future<void> loginWarga(String nik, String noKk) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _repository.loginWarga(nik, noKk);
      final token = response['token'] as String;
      final user = UserModel.fromJson(response);
      await _local.saveSession(token, user.toJson(), user.role);
      state = state.copyWith(isLoading: false, isAuthenticated: true, user: user);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: 'NIK atau No. KK tidak valid');
    }
  }

  Future<void> loginAdmin(String username, String password) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _repository.loginAdmin(username, password);
      final token = response['token'] as String;
      final user = UserModel.fromJson(response);
      await _local.saveSession(token, user.toJson(), user.role);
      state = state.copyWith(isLoading: false, isAuthenticated: true, user: user);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: 'Username atau password tidak valid');
    }
  }

  Future<void> logout() async {
    await _repository.logout();
    await _local.clearSession();
    state = const AuthState(isLoading: false);
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(
    ref.read(authRepositoryProvider),
    ref.read(authLocalProvider),
  );
});
