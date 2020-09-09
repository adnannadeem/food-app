import 'package:deliveryboyapp/src/shared/Product.dart';
import 'package:scoped_model/scoped_model.dart';

class CartModel extends Model {
  List<Product> cart = [];
  double totalCartValue = 0;
  int totalQunty = 0;
  double deliveryCharge = 0;
  String ShopID = '';
  int get total => cart.length;
  double get Charge =>deliveryCharge;
  String get Sid =>ShopID;
  void addProduct(product, shop) {
    deliveryCharge = shop['delivery_charge'].toDouble();
    ShopID = shop['id'].toString();
    int index = cart.indexWhere((i) => i.id == product.id);
    print(index);
    if (index != -1)
      updateProduct(product, product.qty + 1);
    else {
      cart.add(product);
      calculateTotal();
      notifyListeners();
    }
  }

  void removeProduct(product) {
    int index = cart.indexWhere((i) => i.id == product.id);
    cart[index].qty = 1;
    cart.removeWhere((item) => item.id == product.id);
    calculateTotal();
    notifyListeners();
  }

  void updateProduct(product, qty) {
    int index = cart.indexWhere((i) => i.id == product.id);
    cart[index].qty = qty;
    if (cart[index].qty == 0)
     removeProduct(product);
      calculateTotal();
      notifyListeners();
  }

  void clearCart() {
    cart.forEach((f) => f.qty = 1);
    cart = [];
    deliveryCharge = 0;
    totalQunty = 0;
    notifyListeners();
  }

  void calculateTotal() {
    totalCartValue = 0;
    totalQunty = 0;
    cart.forEach((f) {
      totalCartValue += f.price * f.qty;
      totalQunty += f.qty;
    });
  }

}

