class Product {
  int id;
  String name;
  String imgUrl;
  double price;
  int qty;
  int quantity;
  int productItemID;

  Product({this.id, this.name,this.productItemID, this.price, this.qty,this.quantity, this.imgUrl});
}

class ItemProduct {
  String shop_id;
  int product_id;
  double discounted_price;
  double unit_price;
  int quantity;

  ItemProduct({this.shop_id, this.product_id, this.unit_price, this.quantity, this.discounted_price});

  Map<String, dynamic> TojsonData() {
    var map = new Map<String, dynamic>();
    map["shop_id"] = shop_id;
    map["product_id"] = product_id;
    map["unit_price"] = unit_price;
    map["quantity"] = quantity;
    map["discounted_price"] = discounted_price;
    return map;
  }
}
