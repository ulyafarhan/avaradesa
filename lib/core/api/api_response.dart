class ApiResponse<T> {
  final bool success;
  final String? message;
  final T? data;
  final Map<String, List<String>>? errors;

  const ApiResponse({
    required this.success,
    this.message,
    this.data,
    this.errors,
  });

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic)? fromData,
  ) {
    return ApiResponse(
      success: json['message'] != null,
      message: json['message'] as String?,
      data: json['data'] != null && fromData != null ? fromData(json['data']) : null,
      errors: (json['errors'] as Map<String, dynamic>?)?.map(
        (k, v) => MapEntry(k, List<String>.from(v as List)),
      ),
    );
  }
}

class PaginatedResponse<T> {
  final List<T> data;
  final int currentPage;
  final int total;
  final int perPage;
  final int? lastPage;

  const PaginatedResponse({
    required this.data,
    required this.currentPage,
    required this.total,
    required this.perPage,
    this.lastPage,
  });

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic) fromItem,
  ) {
    return PaginatedResponse(
      data: (json['data'] as List).map(fromItem).toList(),
      currentPage: json['current_page'] as int,
      total: json['total'] as int,
      perPage: json['per_page'] as int,
      lastPage: json['last_page'] as int?,
    );
  }
}
