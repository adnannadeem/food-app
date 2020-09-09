import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_slidable/flutter_slidable.dart';
import 'package:deliveryboyapp/config/api.dart';
import 'package:deliveryboyapp/models/cartmodel.dart';
import 'package:deliveryboyapp/src/Widget/CircularLoadingWidget.dart';
import 'package:deliveryboyapp/src/shared/Product.dart';
import 'package:deliveryboyapp/src/utils/CustomTextStyle.dart';
import 'package:provider/provider.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

import '../../main.dart';

class ProductList extends StatefulWidget {

  final String categoryID;
  final String category;
  final  shop;
  final CartModel model;
  ProductList({Key key,  this.category,this.categoryID,this.shop, this.model}) : super(key: key);

  @override
  _ProductAllState createState() => _ProductAllState();
}

class _ProductAllState extends State<ProductList> {
  TextEditingController editingProductsController = TextEditingController();
  GlobalKey<RefreshIndicatorState> refreshKey;

  String api = FoodApi.baseApi;
  List<Product> _products = [];
  List _listProduct = List();
  String token;
  String shopID;

  Future<String> getProducts(String shopID) async {
    final url = "$api/shop-product/$shopID/shop/product";
    var response = await http.get(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _listProduct = resBody['data'];
        _listProduct.forEach((element) => _products.add(Product(name: element['product']['name'], id: element['product']['id'],productItemID:element['id'], imgUrl: element['product']['image'],quantity: element['quantity'] , price: element['unit_price'].toDouble())));
      });
    } else {
      throw Exception('Failed to');
    }
    return "Sucess";
  }

  Future<void> getDelete(String productID) async {
      final url = "$api/shop-product/$shopID/shop/$productID/product";
      final response = await http.delete(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token'});
      var resBody = json.decode(response.body);
      if (response.statusCode == 200) {
        _showAlert(context,true, 'Successfully Delete Product ');
      } else {
        _showAlert(context,false,'Not Successfully Delete Product ');
        throw Exception('Failed to data');
      }


  }
  Future<void> _showAlert(BuildContext context, bool,mes) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Product Update'),
          content:Text(mes),
          actions: <Widget>[
            FlatButton(
              child: Text('Ok'),
              onPressed: () {
                if(bool){
                  Navigator.push(
                      context, MaterialPageRoute(builder: (context) => MyHomePage(tabsIndex:2,title: 'Product',)));
                }else {
                  Navigator.of(context).pop();
                }
              },
            ),
          ],
        );
      },
    );
  }

  Future<Null> refreshList() async {
    setState(() {
      _products.clear();
      this.getProducts(shopID);
    });
  }
  @override
  void initState() {
    super.initState();
     token = Provider.of<AuthProvider>(context,listen: false).token;
    getProducts(shopID);
  }
  @override
  Widget build(BuildContext context) {
    final currency = Provider.of<AuthProvider>(context).currency;

    return Scaffold(
      backgroundColor: Colors.indigo[50],
      body: RefreshIndicator(
      key: refreshKey,
      onRefresh: () async {
          await refreshList();
        },
      child: Stack(
        fit: StackFit.expand,
        children: <Widget>[
          _products.isEmpty ?  ListView(shrinkWrap: true, children:<Widget>[CircularLoadingWidget(height: 500, subtitleText: 'No products found',)],):
          ListView.separated(
            padding: EdgeInsets.symmetric(vertical: 10),
            scrollDirection: Axis.vertical,
            shrinkWrap: true,
            primary: false,
            itemCount: _products.length,
            separatorBuilder: (context, index) {
              return SizedBox(height: 10);
            },
            itemBuilder: (context, index) {
              return
                Slidable(
                actionPane: SlidableDrawerActionPane(),
                actionExtentRatio: 0.25,
                child: Container(
                  color: Colors.white,
                  child:
                  _buildFoodCard(context,currency,_products[index], () {})
                ),
                secondaryActions: <Widget>[
                  IconSlideAction(
                    caption: 'Edit',
                    color: Colors.black45,
                    icon: Icons.edit,
                    onTap: () {

                    },
                  ),
                  IconSlideAction(
                    caption: 'Delete',
                    color: Colors.red,
                    icon: Icons.delete,
                    onTap: () {
                      getDelete(_products[index].productItemID.toString());
                    },
                  ),
                ],
              );
            },

          ),

          Positioned(
            bottom: 10,
            right: 10,
            child:  FloatingActionButton.extended(
              backgroundColor: Theme.of(context).primaryColor,
              onPressed: () {

              },
              isExtended: true,
              materialTapTargetSize: MaterialTapTargetSize.padded,
              icon: Icon(Icons.add_circle_outline),
              label: Text('Add'),
            ),
          ),
        ],
      )
    )

    );
  }
}
Widget _buildFoodCard(context,currency,Product food, onTapped) {
  return
    InkWell(
    splashColor: Theme.of(context).accentColor,
    focusColor: Theme.of(context).accentColor,
    highlightColor: Theme.of(context).primaryColor,
    onTap: onTapped,
    child: Container(
      padding: EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.9),
        boxShadow: [
          BoxShadow(color: Theme.of(context).focusColor.withOpacity(0.1), blurRadius: 5, offset: Offset(0, 2)),
        ],
      ),
      child:
      Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: <Widget>[
          Container(
            child: Container(
              height: 60,
              width: 60,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.all(Radius.circular(5)),
                image: DecorationImage(image: NetworkImage(food.imgUrl), fit: BoxFit.cover),
              ),
            ),
          ),
          SizedBox(width: 15),
          Flexible(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: <Widget>[
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Text(
                        food.name,
                        overflow: TextOverflow.ellipsis,
                        maxLines: 2,
                        style: Theme.of(context).textTheme.subhead,
                      ),
                      Text(
                        'Quantity - '+food.quantity.toString(),
                        overflow: TextOverflow.ellipsis,
                        maxLines: 2,
                        style: Theme.of(context).textTheme.caption,
                      ),
                    ],
                  ),
                ),
                SizedBox(width: 8),
                Text('$currency' + food.price.toString(), style:CustomTextStyle.textFormFieldMedium.copyWith(
                    color: Colors.black54,
                    fontSize: 18,
                    fontWeight: FontWeight.bold
                ),),
              ],
            ),
          )
        ],
      ),
    ),
  );
}

