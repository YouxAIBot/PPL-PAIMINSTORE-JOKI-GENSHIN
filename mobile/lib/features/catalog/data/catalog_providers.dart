import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'catalog_models.dart';
import 'catalog_repository.dart';

final categoriesProvider = FutureProvider<List<Category>>(
        (ref) => ref.watch(catalogRepositoryProvider).categories());

final packagesProvider = FutureProvider.family<List<Package>, String>(
        (ref, slug) => ref.watch(catalogRepositoryProvider).packages(slug));

final packageProvider = FutureProvider.family<Package, int>(
        (ref, id) => ref.watch(catalogRepositoryProvider).package(id));