import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'provider/auth_provider.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _nikCtrl = TextEditingController();
  final _noKkCtrl = TextEditingController();
  final _usernameCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  bool _isAdmin = false;

  @override
  void dispose() {
    _nikCtrl.dispose();
    _noKkCtrl.dispose();
    _usernameCtrl.dispose();
    _passwordCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.home_work_outlined, size: 64, color: Theme.of(context).colorScheme.primary),
                const SizedBox(height: 16),
                Text('AvaraDesa', style: Theme.of(context).textTheme.headlineMedium),
                const SizedBox(height: 8),
                Text('Sistem Informasi Desa', style: Theme.of(context).textTheme.bodyLarge?.copyWith(color: Colors.grey)),
                const SizedBox(height: 32),

                SegmentedButton<bool>(
                  segments: const [
                    ButtonSegment(value: false, label: Text('Warga')),
                    ButtonSegment(value: true, label: Text('Admin')),
                  ],
                  selected: {_isAdmin},
                  onSelectionChanged: (v) => setState(() => _isAdmin = v.first),
                ),
                const SizedBox(height: 24),

                if (!_isAdmin) ...[
                  TextField(
                    controller: _nikCtrl,
                    decoration: const InputDecoration(labelText: 'NIK', hintText: '16 digit NIK'),
                    keyboardType: TextInputType.number,
                    maxLength: 16,
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: _noKkCtrl,
                    decoration: const InputDecoration(labelText: 'No. KK', hintText: '16 digit No. KK'),
                    keyboardType: TextInputType.number,
                    maxLength: 16,
                  ),
                ] else ...[
                  TextField(
                    controller: _usernameCtrl,
                    decoration: const InputDecoration(labelText: 'Username', hintText: 'Masukkan username'),
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: _passwordCtrl,
                    decoration: const InputDecoration(labelText: 'Password', hintText: 'Masukkan password'),
                    obscureText: true,
                  ),
                ],

                if (authState.error != null) ...[
                  const SizedBox(height: 16),
                  Text(authState.error!, style: TextStyle(color: Theme.of(context).colorScheme.error)),
                ],

                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: authState.isLoading ? null : () => _login(),
                    child: authState.isLoading
                        ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Text('Masuk'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _login() {
    if (_isAdmin) {
      ref.read(authProvider.notifier).loginAdmin(_usernameCtrl.text, _passwordCtrl.text);
    } else {
      ref.read(authProvider.notifier).loginWarga(_nikCtrl.text, _noKkCtrl.text);
    }
  }
}
