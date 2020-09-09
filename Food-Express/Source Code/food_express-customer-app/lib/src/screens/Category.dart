
import 'dart:ffi';

import 'package:flutter/material.dart';
import 'package:foodexpress/config/api.dart';
import 'package:foodexpress/main.dart';
import 'package:foodexpress/models/cartmodel.dart';
import 'package:foodexpress/providers/auth.dart';
import 'package:foodexpress/src/Widget/CircularLoadingWidget.dart';
import 'package:foodexpress/src/screens/cartpage.dart';
import 'package:foodexpress/src/screens/loginPage.dart';
import 'package:foodexpress/src/screens/productAll.dart';
import 'package:foodexpress/src/shared/fryo_icons.dart';
import 'package:provider/provider.dart';
import '../shared/styles.dart';
import '../shared/colors.dart';
import './ProductPage.dart';
import '../shared/Product.dart';
import 'dart:async';
import 'dart:convert';
import 'package:scoped_model/scoped_model.dart';

import 'package:http/http.dart' as http;

class Category extends StatefulWidget {

  final String shopID;
  final String shopName;
  final CartModel model;
  Category({Key key, @required this.shopID,this.shopName, this.model}) : super(key: key);

  @override
  _CategoryState createState() => _CategoryState();
}
enum ConfirmAction { CANCEL, ACCEPT }

class _CategoryState extends State<Category> {
  TextEditingController editingProductController = TextEditingController();
  GlobalKey<RefreshIndicatorState> refreshKey;
  int _selectedIndex = 0;
  String _title;
  String _sitename;
  var authenticated;

  String api = FoodApi.baseApi;
  List _categories = List();
  List _listProduct = List();
  List<Product> _products = [];
  Map<String, dynamic> shop = {"id":'',"name" :'', "delivery_charge" :0.0,"opening_time":'',"closing_time":'', "image" :'',"description":'',"address":''};

  Future<String> getCategories(String shopID) async {
    final url = "$api/shops/$shopID/categories";
    var response = await http.get(url, headers: {"Accept": "application/json"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _categories = resBody['data']['categories'];
        shop['id'] = resBody['data']['shop']['id'];
        shop['name'] = resBody['data']['shop']['name'];
        shop['description'] = resBody['data']['shop']['description'];
        shop['delivery_charge'] = resBody['data']['shop']['delivery_charge'] !=null ? resBody['data']['shop']['delivery_charge'] .toDouble():0.0;
        shop['opening_time'] = resBody['data']['shop']['opening_time'];
        shop['closing_time'] = resBody['data']['shop']['closing_time'];
        shop['address'] = resBody['data']['shop']['address'];
        shop['image'] = resBody['data']['shop']['image'];
      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }
  Future<String> getProducts(String shopID) async {
    final url = "$api/shops/$shopID/products";
    var response = await http.get(url, headers: {"Accept": "application/json"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        _listProduct = resBody['data']['products'];
        _listProduct.forEach((element) => _products.add(Product(name: element['name'], stock_count:element['stock_count'],in_stock:element['in_stock'],id: element['id'], imgUrl: element['image'], price: element['unit_price'].toDouble())));
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
        _listProduct.forEach((element) => _products.add(Product(name: element['name'],stock_count:element['stock_count'], in_stock:element['in_stock'],id: element['id'], imgUrl: element['image'], price: element['unit_price'].toDouble())));
      });

    } else {
      throw Exception('Failed to data');
    }
    return;
  }
  Future<Null> refreshList() async {
    setState(() {
      _products.clear();
      _categories.clear();
      this.getCategories(widget.shopID);
      this.getProducts(widget.shopID);
    });
  }
  Future<bool> _onBackPressed() {
    return showDialog(
      context: context,
      barrierDismissible: false, // user must tap button for close dialog!
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Are you sure?'),
          content: const Text(
              'If you click back, the shop will cancel your order'),
          actions: <Widget>[
            FlatButton(
              child: const Text('CANCEL'),
              onPressed: () {
                Navigator.of(context).pop(false);
              },
            ),
            FlatButton(
              child: const Text('ACCEPT'),
              onPressed: () {
                Navigator.of(context).pop(true);
                ScopedModel.of<CartModel>(context, rebuildOnChange: true).clearCart();
              },
            )
          ],
        );
      },
    )?? false;
  }
  @override
  void initState() {
    super.initState();
    this.getCategories(widget.shopID);
    this.getProducts(widget.shopID);
  }
  @override
  Widget build(BuildContext context) {
    final currency = Provider.of<AuthProvider>(context).currency;
    authenticated = Provider.of<AuthProvider>(context).status;
    final token = Provider.of<AuthProvider>(context).token;
    _sitename = Provider.of<AuthProvider>(context).sitename;
    return  ScopedModel.of<CartModel>(context, rebuildOnChange: true).totalQunty != 0 ?   WillPopScope(
        onWillPop:_onBackPressed,
        child:
        ScopedModel<CartModel>(
          model:  CartModel(),
          child: Scaffold(
              backgroundColor: bgColor,
              appBar: AppBar(
                centerTitle: true,
                elevation: 0,
                leading:
                BackButton(
                  color: white,
                ),

                backgroundColor: primaryColor,
                title:
                Text(widget.shopName, textAlign: TextAlign.center),
                actions:
                <Widget>[
                  new Padding(
                    padding: const EdgeInsets.all(10.0),
                    child: new Container(
                      height: 150.0,
                      width: 30.0,
                      child: new GestureDetector(
                        onTap: () {
                          Navigator.push(
                              context, MaterialPageRoute(builder: (context) => CartPage()));
                        },
                        child: Stack(
                          children: <Widget>[
                            new IconButton(
                                icon: new Icon(
                                  Icons.shopping_cart,
                                  color: Colors.white,
                                ),
                                onPressed: (){
                                  Navigator.push(
                                      context, MaterialPageRoute(builder: (context) => CartPage()));
                                }),
                            new Positioned(
                                child: new Stack(
                                  children: <Widget>[
                                    new Icon(Icons.brightness_1,
                                        size: 20.0, color: Colors.orange.shade500),
                                    new Positioned(
                                        top: 4.0,
                                        right: 5.5,
                                        child: new Center(
                                          child: new Text(
                                            ScopedModel.of<CartModel>(context, rebuildOnChange: true).totalQunty.toString(),
                                            style: new TextStyle(
                                                color: Colors.white,
                                                fontSize: 11.0,
                                                fontWeight: FontWeight.w500),
                                          ),
                                        )),
                                  ],
                                )),
                          ],
                        ),
                      ),
                    ),
                  )
                ],
              ),
              body:   RefreshIndicator(
                key: refreshKey,
                onRefresh: () async {
                  await refreshList();
                },
                child:
                storeTab(context,currency,shop, _categories, _products),
              ),
              bottomNavigationBar: BottomNavigationBar(
                items: <BottomNavigationBarItem>[
                  BottomNavigationBarItem(
                      icon: Icon(Fryo.shop),
                      title: Text(
                        'Store',
                        style: tabLinkStyle,
                      )),
                  BottomNavigationBarItem(
                      icon: Icon(Fryo.cart),
                      title: Text(
                        'My Order',
                        style: tabLinkStyle,
                      )),
                  BottomNavigationBarItem(
                      icon: Icon(Fryo.list),
                      title: Text(
                        'Transaction',
                        style: tabLinkStyle,
                      )),
                  BottomNavigationBarItem(
                      icon: Icon(Fryo.user_1),
                      title: Text(
                        'Profile',
                        style: tabLinkStyle,
                      )),
                ],
                type: BottomNavigationBarType.fixed,
                fixedColor: Colors.green[600],
                onTap: _onItemTapped,
              )

          ),
        )
    ):
      ScopedModel<CartModel>(
      model:  CartModel(),
    child: Scaffold(
        backgroundColor: bgColor,
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          leading:
          BackButton(
            color: white,
          ),

          backgroundColor: primaryColor,
          title:
              Text(widget.shopName, textAlign: TextAlign.center),
          actions:
          <Widget>[
            new Padding(
              padding: const EdgeInsets.all(10.0),
              child: new Container(
                height: 150.0,
                width: 30.0,
                child: new GestureDetector(
                  onTap: () {
                    Navigator.push(
                        context, MaterialPageRoute(builder: (context) => CartPage()));
                  },
                  child: Stack(
                    children: <Widget>[
                      new IconButton(
                          icon: new Icon(
                            Icons.shopping_cart,
                            color: Colors.white,
                          ),
                          onPressed: (){
                            Navigator.push(
                                context, MaterialPageRoute(builder: (context) => CartPage()));
                          }),
                           new Positioned(
                          child: new Stack(
                            children: <Widget>[
                              new Icon(Icons.brightness_1,
                                  size: 20.0, color: Colors.orange.shade500),
                              new Positioned(
                                  top: 4.0,
                                  right: 5.5,
                                  child: new Center(
                                    child: new Text(
                                      ScopedModel.of<CartModel>(context, rebuildOnChange: true).totalQunty.toString(),
                                      style: new TextStyle(
                                          color: Colors.white,
                                          fontSize: 11.0,
                                          fontWeight: FontWeight.w500),
                                    ),
                                  )),
                            ],
                          )),
                    ],
                  ),
                ),
              ),
            )
          ],
        ),
        body:   RefreshIndicator(
            key: refreshKey,
            onRefresh: () async {
            await refreshList();
            },
          child:
          storeTab(context,currency,shop, _categories, _products),
        ),
          bottomNavigationBar: BottomNavigationBar(
          items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(
              icon: Icon(Fryo.shop),
                title: Text(
                  'Store',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.cart),
                title: Text(
                  'My Order',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.list),
                title: Text(
                  'Transaction',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.user_1),
                title: Text(
                  'Profile',
                  style: tabLinkStyle,
                )),
            ],
          type: BottomNavigationBarType.fixed,
          fixedColor: Colors.green[600],
            onTap: _onItemTapped,
          )

    ),
    );
  }
  Void _onItemTapped(int index) {
    setState(() {
      ScopedModel.of<CartModel>(context, rebuildOnChange: true).clearCart();
      if(index == 1){
          if(authenticated == Status.Authenticated) {
            Navigator.push(
                context, MaterialPageRoute(builder: (context) => MyHomePage(title:'My Order',tabsIndex: 1,)));
          }else{
            Navigator.push(
                context, MaterialPageRoute(builder: (context) => LoginPage()));
          }
      }else if(index == 2){
        if(authenticated == Status.Authenticated) {
          Navigator.push(
              context, MaterialPageRoute(builder: (context) => MyHomePage(title:'Transaction',tabsIndex: 2,)));
        }else{
          Navigator.push(
              context, MaterialPageRoute(builder: (context) => LoginPage()));
        }
      }else if(index == 3){
        if(authenticated == Status.Authenticated) {
          Navigator.push(
              context, MaterialPageRoute(builder: (context) => MyHomePage(title:'Profile',tabsIndex: 3,)));
        }else{
          Navigator.push(
              context, MaterialPageRoute(builder: (context) => LoginPage()));
        }
      }else {
        Navigator.pop(
            context, MaterialPageRoute(builder: (context) => MyHomePage(title:_sitename,tabsIndex: 0,)));
      }
    });
  }


  storeTab(BuildContext context,currency, shop,List _categories, List<Product> _products,) {
    return
      ListView(
        children: <Widget>[
          shop['name']== '' ? Container():
          Padding(
            padding: EdgeInsets.only( right:0.0),
            child:
            Container(
              child:sectionShop(context,currency,shop, onViewMore: () {}),
            ),
          ),
          _categories.isEmpty ? Container(): Padding(
            padding: EdgeInsets.only( left: 15.0,right: 15.0),
             child:
                  Container(
                    child:headerTopCategories(context,shop, _categories),
                  ),
          ),

          _products.isEmpty ? Container(): Padding(
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
                  SerchProduct(shop['id'].toString(), value != null? value:null);
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
              height: MediaQuery.of(context).size.height /1.49,
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
                        context, MaterialPageRoute(builder: (context) => ProductPage(currency:currency,productData: _products[index],shop:shop)));
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
      child: Stack(
        children: <Widget>[
          Column(
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
          food.in_stock == true ?SizedBox(height: 0,): Positioned(
              top: 0,
              left: 0,
              child: Container(
                padding:
                EdgeInsets.only(top: 5, left: 10, right: 10, bottom: 5),
                decoration: BoxDecoration(
                    color: Colors.grey[600],
                    borderRadius: BorderRadius.circular(50)),
                child: Text('Stock out',
                    style: TextStyle(
                        color: Colors.white, fontWeight: FontWeight.w700)),
              )
          )
      ]
    )
    ),
  );
}

Widget sectionShop(context,currency, shop, {onViewMore}) {
  return
    Padding(
      padding: EdgeInsets.only(left: 15.0, top: 10.0,right: 15.0),
      child: Stack(
        children: <Widget>[
          Container(
            height: 180.0,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(10.0),
              color: Colors.white,
            ),
            child:
            Padding(
              padding: EdgeInsets.all(3.0),
              child: Row(
                children: <Widget>[
                  Image(image: shop['image'] !=null ? NetworkImage(shop['image']): AssetImage('assets/steak.png'),
                      fit: BoxFit.contain,
                    height: 100.0,
                    width: 100.0,
                  ),
                  SizedBox(width: 15.0),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      SizedBox(height: 10.0),
                      Text(shop['name'] !=null ?shop['name'] :'',
                        overflow: TextOverflow.fade,
                        maxLines: 2,
                        softWrap: true,
                        style: TextStyle(
                            color: Color(0xFF563734),
                            fontFamily: 'Montserrat',
                            fontSize: 15.0
                        ),
                      ),
                      SizedBox(height: 10.0),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: <Widget>[
                          Icon(
                            Icons.location_on,
                            size: 17.0,
                            color: Colors.amber.shade500,
                          ),
                          Text(
                            shop['address'] != null ? shop['address'] : '',
                            style:  TextStyle(
                                color: Color(0xFF575E67),
                                fontFamily: 'Varela',
                                fontSize: 11.0),
                            softWrap: false,
                            overflow: TextOverflow.fade,
                          ),
                        ],
                      ),
                      SizedBox(height: 10.0),
                      Container(
                        width: 250.0,
                        child: Text( shop['opening_time'] !=null? 'Opening Time - '+shop['opening_time']:' ',
                          style: TextStyle(
                              color: Color(0xFFB2A9A9),
                              fontFamily: 'Montserrat',
                              fontSize: 11.0
                          ),
                        ),
                      ),
                      SizedBox(height: 5.0),
                      Container(
                        width: 250.0,
                        child: Text(shop['closing_time'] !=null ? 'Closing Time - '+shop['closing_time']:'',
                          style: TextStyle(
                              color: Color(0xFFB2A9A9),
                              fontFamily: 'Montserrat',
                              fontSize: 11.0
                          ),
                        ),
                      ),
                      SizedBox(height: 5.0),
                      shop['description'] !=null ? Container(
                        height: 30.0,
                        width: 250,
                        child: Text(shop['description'] !=null ? shop['description'] : '',
                          overflow: TextOverflow.fade,
                          maxLines: 2,
                          softWrap: true,
                          style: TextStyle(
                              color: Color(0xFFB2A9A9),
                              fontFamily: 'Montserrat',
                              fontSize: 11.0
                          ),
                        ),
                      ): Container(),

                      SizedBox(height: 10.0),
                      Text('Delivery charge '+ '$currency '+ shop['delivery_charge'].toString(),
                        style: TextStyle(
                            color: Color(0xFFF76053),
                            fontFamily: 'Montserrat',
                            fontSize: 13.0
                        ),
                      )
                    ],
                  )
                ],

              ),
            ),
          )
        ],
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
Widget headerTopCategories(context,shop, List _categories) {
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
            return headerCategoryItem(context,f['name'], f['image'],f['id'],shop);
          }).toList(),
        ),
      )
    ],
  );
}

Widget headerCategoryItem(context,String name, String icon, int id,shop) {
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
                  builder: (context) => ProductAllPage(category:name,categoryID: '$id',shop:shop),
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

