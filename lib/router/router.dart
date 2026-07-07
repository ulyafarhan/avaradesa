import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../features/auth/presentation/splash_screen.dart';
import '../features/auth/presentation/login_screen.dart';
import '../features/home/presentation/home_screen.dart';
import '../features/surat/presentation/kategori_list_screen.dart';
import '../features/surat/presentation/buat_surat_screen.dart';
import '../features/surat/presentation/pengajuan_list_screen.dart';
import '../features/surat/presentation/pengajuan_detail_screen.dart';
import '../features/mutasi/presentation/mutasi_screens.dart';
import '../features/informasi/presentation/informasi_screens.dart';
import '../features/fasilitas/presentation/fasilitas_screen.dart';
import '../features/statistik/presentation/statistik_screen.dart';
import '../features/profil/presentation/profile_screen.dart';
import '../features/admin/presentation/admin_screens.dart';
import '../shared/widgets/app_scaffold.dart';

final _rootNavigatorKey = GlobalKey<NavigatorState>();

final routerProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    navigatorKey: _rootNavigatorKey,
    initialLocation: '/login',
    routes: [
      GoRoute(path: '/splash', builder: (_, _) => const SplashScreen()),
      GoRoute(path: '/login', builder: (_, _) => const LoginScreen()),

      StatefulShellRoute.indexedStack(
        builder: (_, _, navigationShell) => MainShell(navigationShell: navigationShell),
        branches: [
          StatefulShellBranch(routes: [
            GoRoute(path: '/home', builder: (_, _) => const HomeScreen()),
          ]),
          StatefulShellBranch(routes: [
            GoRoute(path: '/surat', builder: (_, _) => const PengajuanListScreen()),
          ]),
          StatefulShellBranch(routes: [
            GoRoute(path: '/informasi', builder: (_, _) => const InformasiListScreen()),
          ]),
          StatefulShellBranch(routes: [
            GoRoute(path: '/profil', builder: (_, _) => const ProfileScreen()),
          ]),
        ],
      ),

      GoRoute(path: '/surat/kategori', builder: (_, _) => const KategoriListScreen()),
      GoRoute(path: '/surat/kategori/:id', builder: (_, state) => KategoriListScreen()),
      GoRoute(path: '/surat/buat/:kategoriId', builder: (_, state) => BuatSuratScreen(kategoriId: int.parse(state.pathParameters['kategoriId']!))),
      GoRoute(path: '/surat/pengajuan/:id', builder: (_, state) => PengajuanDetailScreen(id: int.parse(state.pathParameters['id']!))),
      GoRoute(path: '/mutasi', builder: (_, _) => const MutasiListScreen()),
      GoRoute(path: '/mutasi/buat', builder: (_, _) => const BuatMutasiScreen()),
      GoRoute(path: '/informasi/:slug', builder: (_, state) => InformasiDetailScreen(slug: state.pathParameters['slug']!)),
      GoRoute(path: '/statistik', builder: (_, _) => const StatistikScreen()),
      GoRoute(path: '/fasilitas', builder: (_, _) => const FasilitasScreen()),
      GoRoute(path: '/profil/keluarga', builder: (_, _) => const ProfilKeluargaScreen()),
      GoRoute(path: '/pengaturan', builder: (_, _) => const PengaturanScreen()),

      GoRoute(path: '/admin/home', builder: (_, _) => const AdminDashboardScreen()),
      GoRoute(path: '/admin/surat', builder: (_, _) => const AdminSuratScreen()),
      GoRoute(path: '/admin/surat/:id', builder: (_, state) => AdminSuratDetailScreen(id: int.parse(state.pathParameters['id']!))),
      GoRoute(path: '/admin/mutasi', builder: (_, _) => const AdminMutasiScreen()),
      GoRoute(path: '/admin/mutasi/:id', builder: (_, state) => AdminMutasiDetailScreen(id: int.parse(state.pathParameters['id']!))),
      GoRoute(path: '/admin/informasi', builder: (_, _) => const AdminInformasiScreen()),
    ],
  );
});
