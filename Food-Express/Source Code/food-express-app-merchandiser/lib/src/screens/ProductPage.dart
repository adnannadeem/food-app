import 'package:flutter/material.dart';
import 'package:foodshopapp/config/api.dart';
import 'package:foodshopapp/models/cartmodel.dart';
import 'package:foodshopapp/providers/auth.dart';
import 'package:foodshopapp/src/Widget/CircularLoadingWidget.dart';
import 'package:foodshopapp/src/utils/CustomTextStyle.dart';
import 'package:provider/provider.dart';
import '../shared/Product.dart';
import '../shared/styles.dart';
import '../shared/colors.dart';
import '../shared/buttons.dart';
import 'package:scoped_model/scoped_model.dart';
import 'package:carousel_pro/carousel_pro.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;

import 'cartpage.dart';

class GroupModelOptions {
  String id;
  String name;
  String price;
  GroupModelOptions({this.name, this.price,this.id});
}
class GroupModelVariations {
  String id;
  String name;
  String price;
  GroupModelVariations({this.name, this.price,this.id});
}

class ProductPage extends StatefulWidget {
  final String pageTitle;
  final Product productData;
  final String currency;

  ProductPage({Key key, this.pageTitle, this.currency,this.productData}) : super(key: key);

  @override
  _ProductPageState createState() => _ProductPageState();
}

class _ProductPageState extends State<ProductPage> {
  String api = FoodApi.baseApi;
  List _listImage = List();
  List _variations = List();
  List _options = List();
  List  Image = [AssetImage('assets/images/icon.png')];

  int _quantity = 1;
  int count = 0;

  String _currOption = '1';
  String _currVariation = '1';
  String deliveryCharge;
  String shopID;

  List<GroupModelVariations> _groupVariations = [];
  List<GroupModelOptions> _groupOptions = [];
  Map<String, dynamic> ProductShow = {"id":'',"name" :'',"unit_price": '', "stock_count":'',"in_stock":'',"description":'',};

  Future<String> getProduct(String shopID, String ProductID) async {
    final url = "$api/shops/$shopID/products/$ProductID";
    var response = await http.get(url, headers: {"Accept": "application/json"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      print(resBody);
      setState(() {
        ProductShow['id'] = resBody['data']['id'];
        ProductShow['name'] = resBody['data']['name'];
        ProductShow['unit_price'] = resBody['data']['unit_price'].toString();
        ProductShow['stock_count'] = resBody['data']['stock_count'];
        ProductShow['in_stock'] = resBody['data']['in_stock'];
        ProductShow['description'] = resBody['data']['description'];
        _listImage = resBody['data']['image'];
        _variations = resBody['data']['variations'];
        _options = resBody['data']['options'];
        Image.clear();
        _listImage.forEach((f)=> Image.add(NetworkImage(f)));
        _variations.forEach((variation)=> _groupVariations.add( GroupModelVariations(id: variation['id'].toString(), name: variation['name'], price:variation['unit_price'].toString(),)));
        _options.forEach((option)=> _groupOptions.add( GroupModelOptions(id: option['id'].toString(), name: option['name'], price:option['unit_price'].toString(),)));
      });
    } else {
      throw Exception('Failed to');
    }
    return "Sucess";
  }
  List _selecteCategorys = List();
  List selecteOptions = [];

  void _onCategorySelected(bool selected, category_id,options) {
    if (selected == true) {
      setState(() {
        _selecteCategorys.add(category_id);
        selecteOptions.add(options);
        print(selecteOptions.length);
      });
    } else {
      setState(() {
        selecteOptions.removeWhere((item) => item.id == category_id);
        _selecteCategorys.remove(category_id);
      });
    }
  }
  @override
  void initState() {
    super.initState();
     shopID = Provider.of<AuthProvider>(context,listen: false).shopID;
     deliveryCharge = Provider.of<AuthProvider>(context,listen: false).deliveryCharge;

    getProduct(shopID,(widget.productData.id).toString());
    widget.productData.qty =_quantity;
  }
  @override
  Widget build(BuildContext context) {
    final authenticated = Provider.of<AuthProvider>(context).status;
    final currency = Provider.of<AuthProvider>(context).currency;

    void _showToast(BuildContext context) {
      final scaffold = Scaffold.of(context);
      scaffold.showSnackBar(
        SnackBar(
          content: const Text('Added to cart'),
          action: SnackBarAction(
              label: 'Dismiss', onPressed: scaffold.hideCurrentSnackBar),
        ),
      );
    }
    void _showToastStock(BuildContext context) {
      final scaffold = Scaffold.of(context);
      scaffold.showSnackBar(
        SnackBar(
          content: const Text('Stock Out'),
          action: SnackBarAction(
              label: 'Dismiss', onPressed: scaffold.hideCurrentSnackBar),
        ),
      );
    }


    Widget imageCarousel = Container(
      height: 300.0,
      child: Carousel(
        boxFit: BoxFit.cover,
        images: Image,
        autoplay: false,
        animationCurve: Curves.fastOutSlowIn,
        animationDuration: Duration(milliseconds: 1000),
        dotSize: 4.0,
        indicatorBgPadding: 8.0,
        dotColor: Colors.red,

      ),
    );
    return Scaffold(
        backgroundColor: bgColor,
        appBar: AppBar(
          elevation: 0,
          backgroundColor: Colors.green,
          centerTitle: true,
          leading: BackButton(
            color: Colors.white,
          ),
          title: Text(widget.productData.name, style: TextStyle(color: Colors.white),),
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
        body:SafeArea(
            child:
            widget.productData == null ? CircularLoadingWidget(height: 500):
            ScopedModelDescendant<CartModel>(
                builder: (context, child, model) {

                  return Column(
                    children: <Widget>[
                      Expanded(
                        child:
                        Container(
                            child:
                            ListView(
                                children: <Widget>[
                                  imageCarousel,
                                  Padding(
                                    padding: const EdgeInsets.only(right: 20, left: 20, bottom: 10, top: 25),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: <Widget>[
                                        Expanded(
                                          child:
                                          Text(
                                            ProductShow['name'],
                                            overflow: TextOverflow.fade,
                                            softWrap: true,
                                            maxLines: 2,
                                            style:CustomTextStyle.textFormFieldMedium.copyWith(
                                                color: Colors.black54,
                                                fontSize: 22,
                                                fontWeight: FontWeight.bold
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Padding(
                                    padding: const EdgeInsets.only(right: 20, left: 20, bottom: 10, top: 5),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: <Widget>[
                                        Expanded(
                                          child:
                                          Text(
                                            'Price '+ currency + ProductShow['unit_price'].toString(),
                                            overflow: TextOverflow.fade,
                                            softWrap: true,
                                            maxLines: 2,
                                            style:CustomTextStyle.textFormFieldMedium.copyWith(
                                                color: Colors.amberAccent,
                                                fontSize: 18,
                                                fontWeight: FontWeight.bold
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),

                                  Padding(
                                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 5),
                                    child: Text(
                                      ProductShow['description'] !=null? ProductShow['description']:'',
                                      overflow: TextOverflow.fade,
                                      style:CustomTextStyle.textFormFieldMedium.copyWith(
                                          color: Colors.black54,
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold
                                      ),
                                    ),
                                  ),
                                  _groupVariations.isEmpty?Container():Column(children: <Widget>[
                                    Container(
                                      child:
                                      ListTile(title:
                                      Text(
                                        'Variation',
                                        overflow: TextOverflow.fade,
                                        softWrap: true,
                                        maxLines: 2,
                                        style:CustomTextStyle.textFormFieldMedium.copyWith(
                                            color: Colors.black,
                                            fontSize: 18,
                                            fontWeight: FontWeight.bold
                                        ),
                                      ),
                                      ),),
                                    Container(
                                        child: Container(
                                          child: Column(
                                            children: _groupVariations
                                                .map((t) =>  RadioListTile(
                                              value: t.id,
                                              groupValue: _currVariation,
                                              title: Text(
                                                "${t.name}",
                                                overflow: TextOverflow.fade,
                                                softWrap: true,
                                                maxLines: 1,
                                                style:CustomTextStyle.textFormFieldMedium.copyWith(
                                                    color: Colors.black,
                                                    fontSize: 16,
                                                    fontWeight: FontWeight.normal
                                                ),
                                              ),
                                              onChanged: (val) {
                                                setState(() {
                                                  _currVariation = val;
                                                  ProductShow['unit_price'] = t.price;
                                                });
                                              },
                                              activeColor: Colors.red,
                                              secondary: OutlineButton(
                                                child: Text(currency + t.price),
                                                onPressed: () {},
                                              ),
                                            )).toList(),
                                          ),
                                        )),
                                  ]),
                                  _groupVariations.isEmpty?Container():SizedBox(height: 5,),
                                  _groupOptions.isEmpty?Container():Column(children: <Widget>[
                                    Container(
                                      child:
                                      ListTile(title:
                                      Text(
                                        'Options',
                                        overflow: TextOverflow.fade,
                                        softWrap: true,
                                        maxLines: 2,
                                        style:CustomTextStyle.textFormFieldMedium.copyWith(
                                            color: Colors.black,
                                            fontSize: 18,
                                            fontWeight: FontWeight.bold
                                        ),
                                      ),
                                      ),),
                                    Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 15,),
                                        child: Container(
                                          child: ListView.builder(
                                              shrinkWrap: true,
                                              primary: false,
                                              itemCount: _groupOptions.length,
                                              itemBuilder: (BuildContext context, int index) {
                                                return CheckboxListTile(
                                                  title: Text(
                                                    _groupOptions[index].name,
                                                    overflow: TextOverflow.fade,
                                                    softWrap: true,
                                                    maxLines: 2,
                                                    style:CustomTextStyle.textFormFieldMedium.copyWith(
                                                        color: Colors.black,
                                                        fontSize: 16,
                                                        fontWeight: FontWeight.normal
                                                    ),
                                                  ),
                                                  subtitle: Text(
                                                    '\$'+ _groupOptions[index].price,
                                                    overflow: TextOverflow.fade,
                                                    softWrap: true,
                                                    maxLines: 2,
                                                    style:CustomTextStyle.textFormFieldMedium.copyWith(
                                                        color: Colors.black,
                                                        fontSize: 14,
                                                        fontWeight: FontWeight.normal
                                                    ),
                                                  ),
                                                  value: _selecteCategorys
                                                      .contains(_groupOptions[index].id),
                                                  onChanged: (bool selected) {
                                                    _onCategorySelected(selected,
                                                        _groupOptions[index].id, _groupOptions[index]);
                                                  },
                                                );
                                              }),
                                        )),
                                  ],)
                                ]
                            )
                        ),
                        flex: 90,
                      ),


                      Expanded(
                        child:
                        Container(
                            width: double.infinity,
                            margin: EdgeInsets.symmetric(vertical: 8, horizontal: 12),
                            child: widget.productData.in_stock == false?Container():
                            Container(
                              child:
                              Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: <Widget>[
                                  Container(
                                    width: 55,
                                    height: 40,
                                    child: OutlineButton(
                                      onPressed: () {
                                        setState(() {
                                          if( widget.productData.in_stock){
                                            if(_quantity == 1) return;
                                            _quantity -= 1;
                                            widget.productData.qty =_quantity;
                                          }

                                        });
                                      },
                                      child: Icon(Icons.remove),
                                    ),
                                  ),
                                  Container(
                                    margin:
                                    EdgeInsets.only(left: 20, right: 20),
                                    child: Text(_quantity.toString(), style: h3),
                                  ),
                                  Container(
                                    width: 55,
                                    height: 40,
                                    child: OutlineButton(
                                      onPressed: () {
                                        setState(() {
                                          if( widget.productData.in_stock) {
                                            if((widget.productData.stock_count -_quantity) == 0){
                                              _showToastStock(context);
                                            }else{
                                              _quantity += 1;
                                              widget.productData.qty = _quantity;
                                            }
                                          }
                                        });
                                      },
                                      child: Icon(Icons.add),
                                    ),
                                  ),
                                  Container(
                                    width: 180,
                                    height: 45,
                                    margin:
                                    EdgeInsets.only(left: 20,),
                                    child: froyoFlatBtn('Add to Cart', (){
                                      if((ProductShow['stock_count']-_quantity)== 0){
                                        _showToastStock(context);
                                      }else{
                                        int total=0;
                                        selecteOptions.forEach((element) =>  total = (total + int.parse(element.price)));
                                        model.addProduct(ProductShow['id'],ProductShow['name'],(total+ int.parse(ProductShow['unit_price'])).toDouble(),_quantity,widget.productData.imgUrl, _currVariation,selecteOptions,shopID,deliveryCharge);
                                      }
                                    }),
                                  ),
                                ],
                              ),
                            )
                        ),
                        flex: 10,
                      )
                    ],
                  );
                })
        )
    );
  }
}
