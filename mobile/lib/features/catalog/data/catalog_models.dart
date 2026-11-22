class Category {
  final int id;
  final String name;
  final String slug;
  const Category({required this.id, required this.name, required this.slug});

  factory Category.fromJson(Map<String, dynamic> j) =>
      Category(id: j['id'], name: j['name'], slug: j['slug']);
}

class Package {
  final int id;
  final String name;
  final String? tier;
  final String? description;
  final int price;
  final int estimatedHours;
  const Package({
    required this.id,
    required this.name,
    this.tier,
    this.description,
    required this.price,
    required this.estimatedHours,
  });

  factory Package.fromJson(Map<String, dynamic> j) => Package(
    id: j['id'],
    name: j['name'],
    tier: j['tier'],
    description: j['description'],
    price: (j['price'] as num).toInt(),
    estimatedHours: j['estimated_hours'],
  );
}