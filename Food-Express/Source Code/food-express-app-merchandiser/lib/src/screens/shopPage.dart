import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:foodshopapp/config/api.dart';
import 'package:foodshopapp/providers/auth.dart';
import 'package:foodshopapp/src/Widget/CircularLoadingWidget.dart';
import 'package:foodshopapp/src/screens/productAll.dart';
import 'package:foodshopapp/src/shared/Product.dart';
import 'package:foodshopapp/src/shared/styles.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:geolocator/geolocator.dart';

import 'ProductPage.dart';


class ShopPage extends StatefulWidget {
  @override
  _ShopPageState createState() {
    return new _ShopPageState();
  }
}


class _ShopPageState extends State<ShopPage> {
  TextEditingController editingProductController = TextEditingController();
  GlobalKey<RefreshIndicatorState> refreshKey;
  Position _currentPosition;
  Geolocator _geolocator = Geolocator();
  final Geolocator geolocator = Geolocator()..forceAndroidLocationManager;
  void checkPermission() {
    _geolocator.checkGeolocationPermissionStatus().then((status) { print('status: $status'); });
    _geolocator.checkGeolocationPermissionStatus(locationPermission: GeolocationPermission.locationAlways).then((status) { print('always status: $status'); });
    _geolocator.checkGeolocationPermissionStatus(locationPermission: GeolocationPermission.locationWhenInUse)..then((status) { print('whenInUse status: $status'); });
  }


  String api = FoodApi.baseApi;
  List _categories = List();
  List _listProduct = List();
  List<Product> _products = [];
  String token;
  String shop;

  Future<String> getCategories(String shopID) async {
    final url = "$api/shops/$shopID/categories";
    var response = await http.get(url, headers: {"Accept": "application/json"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _categories = resBody['data']['categories'];
      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }
  Future<String> getProducts(String shopID) async {
    final url = "$api/shop-product/$shopID/shop/product";
    var response = await http.get(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _listProduct = resBody['data'];
        _listProduct.forEach((element) => _products.add(Product(name: element['product']['name'], id: element['product']['id'], imgUrl: element['product']['image'],quantity: element['quantity'] , price: element['unit_price'].toDouble())));
      });
    } else {
      throw Exception('Failed to');
    }
    return "Sucess";
  }
  void SerchProduct(shop,value) async {
    final url = "$api/search/$shop/shops/$value/products";
    var response = await http.get(url, headers: {"Accept": "application/json"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _products.clear();
        _listProduct = resBody['data'];
        _listProduct.forEach((element) => _products.add(Product(name: element['name'], id: element['id'], imgUrl: element['image'], price: element['unit_price'].toDouble())));
      });

    } else {
      throw Exception('Failed to data');
    }
    return;
  }
  Future<Null> refreshList() async {
    final shop = Provider.of<AuthProvider>(context,listen: false).shopID;
    setState(() {
      _products.clear();
      _categories.clear();
      this.getCategories(shop);
      this.getProducts(shop);
    });
  }

    @override
    void initState() {
      super.initState();
       shop = Provider.of<AuthProvider>(context,listen: false).shopID;
      token = Provider.of<AuthProvider>(context,listen: false).token;
      this.getCategories(shop);
      this.getProducts(shop);
      _getCurrentLocation();
      checkPermission();
    }
  _getCurrentLocation() {
    geolocator
        .getCurrentPosition(desiredAccuracy: LocationAccuracy.best)
        .then((Position position) {
      checkPermission();
      setState(() {
        _currentPosition = position;
      });
    }).catchError((e) {
      print(e);
      checkPermission();
    });
  }
   @override
  Widget build(BuildContext context) {
     final currency = Provider.of<AuthProvider>(context,listen: false).currency;
     return Scaffold(
        backgroundColor: Color(000),
         body: SafeArea(
          child:
          RefreshIndicator(
            key: refreshKey,
            onRefresh: () async {
              await refreshList();
            },
            child:
            storeTab(context,currency, _categories, _products),
          ),        )
    );
  }

  storeTab(BuildContext context,currency,List _categories, List<Product> _products,) {
    return
      ListView(
        shrinkWrap: true,
          children: <Widget>[
            _categories.isEmpty ? Container(): Padding(
              padding: EdgeInsets.only( left: 15.0,right: 15.0),
              child:
              Container(
                child:headerTopCategories(context, _categories),
              ),
            ),

            _products.isEmpty ? Container():
            Padding(
              padding: EdgeInsets.only(top: 15.0, left: 15.0, right: 15.0),
              child:
              Container(
                decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.only(
                      bottomRight: Radius.circular(10.0),
                      bottomLeft: Radius.circular(10.0),
                      topLeft: Radius.circular(10.0),
                      topRight: Radius.circular(10.0),
                    )),
                child: TextField(
                  textInputAction: TextInputAction.search,
                  onSubmitted: (value){
                    SerchProduct(shop, value != null? value:null);
                  },
                  controller: editingProductController,
                  decoration: InputDecoration(
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.only(top: 14.0),
                    hintText: 'Search for  products',
                    hintStyle:
                    TextStyle(fontFamily: 'Montserrat', fontSize: 14.0),
                    prefixIcon: Icon(Icons.search, color: Colors.grey),
                  ),
                ),
              ),
            ),

            SizedBox(height: 10.0),
            _products.isEmpty ? CircularLoadingWidget(height: 600, subtitleText: 'No product and categories  found',):
            Container(
                height: MediaQuery.of(context).size.height /1.87,
                width: MediaQuery.of(context).size.width/2,
                child:
                new GridView.builder(
                    shrinkWrap: true,
                    primary: false,
                    padding: EdgeInsets.all(8.0),
                    itemCount: _products.length,
                    gridDelegate: SliverGridDelegateWithFixedCrossAxisCount( crossAxisCount: 2, mainAxisSpacing: 8, crossAxisSpacing: 8, childAspectRatio: 0.8),
                    itemBuilder: (context, index) {
                      return _buildFoodCard(context,currency,_products[index], () {
                        Navigator.push(
                          context, MaterialPageRoute(builder: (context) {
                          return new ProductPage(currency:currency,productData: _products[index]);
                        }),
                        );
                      });
                    })
            ),
            SizedBox(height: 20.0),


          ]);
  }

}
Widget _buildFoodCard(context,currency,Product food, onTapped) {
  return InkWell(
    highlightColor: Colors.transparent,
    splashColor: Colors.white,
    onTap: onTapped,
    child: Container(
      margin: EdgeInsets.all(5),
      padding: EdgeInsets.all(15),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.all(Radius.circular(5)),
          boxShadow: [
            BoxShadow(color: Theme.of(context).focusColor.withOpacity(0.05), offset: Offset(0, 5), blurRadius: 5)
          ]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: <Widget>[
          Container(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(5.0),
              child: Image.network(
                food.imgUrl,
                fit: BoxFit.cover,
                height: 100,
                width: double.infinity,

              ),

            ),
          ),
          SizedBox(height: 7),
          Text(
            food.name != null ? food.name : '',
            style:  TextStyle(
                fontFamily: 'Montserrat',
                color: Color(0xFF440206),
                fontSize: 15.0),
            softWrap: false,
            maxLines: 1,
            overflow: TextOverflow.fade,
          ),
          SizedBox(height: 4),
          Text(
            '$currency' + food.price.toString(),
            style:  TextStyle(
                fontFamily: 'Montserrat',
                color: Color(0xFFF75A4C),
                fontSize: 14.0),
            softWrap: false,
            maxLines: 1,
            overflow: TextOverflow.fade,
          ),
          SizedBox(height: 17),
        ],
      ),
    ),
  );
}

Widget sectionHeader(String headerTitle, {onViewMore}) {
  return Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    crossAxisAlignment: CrossAxisAlignment.start,
    children: <Widget>[
      Container(
        margin: EdgeInsets.only(left: 15, top: 10),
        child: Text(headerTitle, style: h4),
      ),
    ],
  );
}
// wrap the horizontal listview inside a sizedBox..
Widget headerTopCategories(context, List _categories) {
  return Column(
    mainAxisAlignment: MainAxisAlignment.center,
    crossAxisAlignment: CrossAxisAlignment.center,
    children: <Widget>[
      sectionHeader('All Categories', onViewMore: () {}),
      SizedBox(
        height: 130,
        child: ListView(
          scrollDirection: Axis.horizontal,
          shrinkWrap: true,
          children: _categories.map((f){
            return headerCategoryItem(context,f['name'], f['image'],f['id']);
          }).toList(),
        ),
      )
    ],
  );
}

Widget headerCategoryItem(context,String name, String icon, int id) {
  return Container(
    width: 70,
    margin: EdgeInsets.only(left: 15),
    child: Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: <Widget>[
        Container(
            margin: EdgeInsets.only(bottom: 10),
            width: 70,
            height: 70,
            child: FlatButton(
              color: Colors.white,
              onPressed:() {
                Navigator.of(context).push(MaterialPageRoute(
                  builder: (context) => ProductAllPage(category:name,categoryID: '$id'),
                )
                );
              },
              child: Image(
                image: icon != null ? NetworkImage(icon): AssetImage('assets/steak.png'),
                fit: BoxFit.contain,
                width: 150,
                height: 150,
              ),
            )),
        Text(name ,
          overflow: TextOverflow.fade,
          maxLines: 1,
          softWrap: false,
          style: categoryText,)
      ],
    ),
  );
}