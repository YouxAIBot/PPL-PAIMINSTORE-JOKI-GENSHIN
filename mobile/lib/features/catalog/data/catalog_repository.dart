import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'catalog_models.dart';

abstract class CatalogRepository {
  Future<List<Category>> categories();
  Future<List<Package>> packages(String slug);
  Future<Package> package(int id);
}

class FakeCatalogRepository implements CatalogRepository {
  static const _cats = [
    Category(id: 1, name: 'Joki Explore Map', slug: 'joki-explore-map'),
    Category(id: 2, name: 'Joki Satuan', slug: 'joki-satuan'),
    Category(id: 3, name: 'Joki Quest', slug: 'joki-quest'),
  ];

  static const _pkgs = {
    'joki-explore-map': [
      Package(id: 1, name: 'Mondstadt 100%', price: 75000, estimatedHours: 36),
      Package(id: 2, name: 'Liyue 100%', price: 120000, estimatedHours: 60),
      Package(id: 3, name: 'Inazuma 100%', price: 150000, estimatedHours: 72),
      Package(id: 4, name: 'Sumeru 100%', price: 210000, estimatedHours: 96),
      Package(id: 5, name: 'Fontaine 100%', price: 175000, estimatedHours: 60),
      Package(id: 6, name: 'Natlan 100%', price: 185000, estimatedHours: 72),
      Package(id: 7, name: 'Nod-Krai 100%', price: 165000, estimatedHours: 60),
      Package(id: 8, name: 'Snezhnaya 100%', price: 220000, estimatedHours: 96),
    ],
    'joki-satuan': [
      Package(id: 9, name: 'Spiral Abyss Floor 9-12', price: 45000, estimatedHours: 12),
    ],
    'joki-quest': [
      Package(id: 10, name: 'Quest Archon Fontaine', price: 60000, estimatedHours: 24),
    ],
  };

  @override
  Future<List<Category>> categories() async {
    await Future.delayed(const Duration(milliseconds: 400));
    return _cats;
  }

  @override
  Future<List<Package>> packages(String slug) async {
    await Future.delayed(const Duration(milliseconds: 400));
    return _pkgs[slug] ?? [];
  }

  @override
  Future<Package> package(int id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    return _pkgs.values.expand((e) => e).firstWhere((p) => p.id == id);
  }
}

// Diisi di Langkah 8.
class ApiCatalogRepository implements CatalogRepository {
  @override
  Future<List<Category>> categories() => throw UnimplementedError();
  @override
  Future<List<Package>> packages(String slug) => throw UnimplementedError();
  @override
  Future<Package> package(int id) => throw UnimplementedError();
}

final catalogRepositoryProvider = Provider<CatalogRepository>((ref) {
  const useFake = bool.fromEnvironment('USE_FAKE', defaultValue: true);
  return useFake ? FakeCatalogRepository() : ApiCatalogRepository();
});