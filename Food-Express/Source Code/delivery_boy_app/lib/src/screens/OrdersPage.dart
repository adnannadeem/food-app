import 'dart:ffi';
import 'dart:io';
import 'package:deliveryboyapp/src/utils/CustomTextStyle.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:deliveryboyapp/config/api.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'package:deliveryboyapp/src/Widget/CircularLoadingWidget.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:geolocator/geolocator.dart';

import 'orderhistoryView.dart';

class OrderPage extends StatefulWidget {
  OrderPage({Key key,}) : super(key: key);

  @override
  _OrderPageState createState() {
    return new _OrderPageState();
  }
}

class Item {
  final String deliveryDate;
  final String deliveryTime;
  final String oderId;
  final String oderAmount;
  final String status_name;
  final String status;
  final String oderCode;
  final String deliveryType;
  final String paymentMethod;
  final String paymentStatus;
  final String address;
  final String user;
  final List Items;

  Item(
      {
        this.deliveryDate,
        this.deliveryTime,
        this.oderId,
        this.oderAmount,
        this.deliveryType,
        this.address,
        this.oderCode,
        this.status_name,
        this.status,
        this.user,
        this.Items, this.paymentMethod,this.paymentStatus});
}

class _OrderPageState extends State<OrderPage> {
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
  List resOrder = List();
  List<Item> itemList = <Item>[];
  String currency;

  Future<String> getmyOrder(token) async {
    print('get my prder list');
    print(token);
    final url = "$api/notification-order";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    print(resBody);
    if (response.statusCode == 200) {
      setState(() {
        resOrder = resBody['data'];
        resOrder.forEach((element) {
          var order = json.decode(element['misc']);
          itemList.add(Item(
            deliveryTime: element['time_format'],
            deliveryDate: element['date'],
            oderId: '${element['id']}',
            oderCode: '${order['order_code']}',
            paymentMethod: element['payment_status'].toString() == '10'?'Cash on delivery':'Paid',
          ));
        });

      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }

  Future<Void> orderUpdate(String id, String status,token) async {
    final url = "$api/notification-order/$id/update?status=$status";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        if(status=='5'){
          Navigator.push(
            context, MaterialPageRoute(builder: (context) {
            return new OrderViewPage(orderID: id,currency:currency);
          }),
          );
        }else{
          refreshList(token);
        }
      });
    } else {
      throw Exception('Failed to data');
    }

  }
  Future<void> _showAlert(BuildContext context,status,id) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Order'),
          content: status == '5'?Text('Successfully Accept Order'):Text('Successfully Cancel Order'),
          actions: <Widget>[
            FlatButton(
              child: Text('Ok'),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }
  Future<void> _showConfirmOrderAlert(BuildContext context,id ,status,token) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Order Confirmation'),
          content: status == '5'?Text('are you sure to accept this order'):Text('are you sure to cancel this order'),
          actions: <Widget>[
            FlatButton(
              child: Text('Dismiss'),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            FlatButton(
              child: Text('Confirm'),
              onPressed: () {
                orderUpdate(id, status,token);
                Navigator.of(context).pop();
              },
            ),

          ],
        );
      },
    );
  }

  Future<Null> refreshList(String token) async {
    setState(() {
      itemList.clear();
      this.getmyOrder(token);
    });
  }

  @override
  void initState() {
    super.initState();
    final token = Provider.of<AuthProvider>(context,listen: false).token;
    currency = Provider.of<AuthProvider>(context,listen: false).currency;
    getmyOrder(token);
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
    final token = Provider.of<AuthProvider>(context).token;

    return Scaffold(
        backgroundColor: Colors.indigo[50],
        body: RefreshIndicator(
            key: refreshKey,
            onRefresh: () async {
              await refreshList(token);
            },
            child:
            itemList.isEmpty ?  ListView(children:<Widget>[CircularLoadingWidget(height: 500, subtitleText: 'No Orders found',)],):
            ListView.builder(
                itemCount: itemList.length,
                itemBuilder: (BuildContext cont, int ind) {
                  return SafeArea(
                      child:  InkWell(
                          onTap: () {},
                          child:
                          Column(children: <Widget>[
                            Container(
                                margin: EdgeInsets.only(left: 5.0, right: 5.0, bottom: 5.0),
                                child:
                                Card(
                                    elevation: 0.0,
                                    color: Colors.white,
                                    child:
                                    Container(
                                        padding: const EdgeInsets.fromLTRB(
                                            10.0, 10.0, 10.0, 10.0),
                                        child: GestureDetector(
                                            child: Column(
                                              mainAxisSize: MainAxisSize.max,
                                              mainAxisAlignment: MainAxisAlignment.start,
                                              children: <Widget>[
                                                // three line description
                                                Row(
                                                  mainAxisAlignment: MainAxisAlignment.start,
                                                  children: <Widget>[
                                                    Icon(
                                                      Icons.alarm,
                                                      size: 20.0,
                                                      color: Colors.black,
                                                    ),
                                                    Text(
                                                      ' '+itemList[ind].deliveryTime,
                                                      style: TextStyle(
                                                        fontSize: 16.0,
                                                        fontStyle: FontStyle.normal,
                                                        color: Colors.black87,
                                                      ),
                                                    ),
                                                  ],
                                                ),

                                                Container(
                                                  margin: EdgeInsets.only(top: 3.0),
                                                ),
                                                Divider(
                                                  height: 10.0,
                                                  color: Colors.indigo[100],
                                                ),

                                                Row(
                                                  mainAxisSize: MainAxisSize.max,
                                                  mainAxisAlignment:
                                                  MainAxisAlignment.spaceBetween,
                                                  children: <Widget>[
                                                    Container(
                                                        padding: EdgeInsets.all(3.0),
                                                        child: Column(
                                                          mainAxisAlignment:
                                                          MainAxisAlignment.center,
                                                          children: <Widget>[
                                                            Text(
                                                              'Order Code',
                                                              style: TextStyle(
                                                                  fontSize: 13.0,
                                                                  color: Colors.black54),
                                                            ),
                                                            Container(
                                                              margin: EdgeInsets.only(top: 3.0),
                                                              child: Text(
                                                                itemList[ind].oderCode,
                                                                style: TextStyle(
                                                                    fontSize: 15.0,
                                                                    color: Colors.black87),
                                                              ),
                                                            )
                                                          ],
                                                        )),
                                                    Container(
                                                        padding: EdgeInsets.all(3.0),
                                                        child: Column(
                                                          mainAxisAlignment:
                                                          MainAxisAlignment.center,
                                                          children: <Widget>[
                                                            Text(
                                                              'Date',
                                                              style: TextStyle(
                                                                  fontSize: 13.0,
                                                                  color: Colors.black54),
                                                            ),
                                                            Container(
                                                              margin: EdgeInsets.only(top: 3.0),
                                                              child: Text(
                                                                itemList[ind].deliveryDate,
                                                                style: TextStyle(
                                                                    fontSize: 15.0,
                                                                    color: Colors.black87),
                                                              ),
                                                            ),
                                                          ],
                                                        )),
                                                    Container(
                                                        padding: EdgeInsets.all(3.0),
                                                        child: Column(
                                                          mainAxisAlignment:
                                                          MainAxisAlignment.center,
                                                          children: <Widget>[
                                                            Text(
                                                              'Payment Type',
                                                              style: TextStyle(
                                                                  fontSize: 13.0,
                                                                  color: Colors.black54),
                                                            ),
                                                            Container(
                                                              margin: EdgeInsets.only(top: 3.0),
                                                              child: Text(
                                                                itemList[ind].paymentMethod,
                                                                style: TextStyle(
                                                                    fontSize: 15.0,
                                                                    color: Colors.black87),
                                                              ),
                                                            )
                                                          ],
                                                        )),
                                                  ],
                                                ),
                                                Divider(
                                                  height: 10.0,
                                                  color: Colors.indigo[100],
                                                ),
                                                Container(
                                                  child: new Row(
                                                    mainAxisAlignment: MainAxisAlignment.center,
                                                    children: <Widget>[
                                                      RaisedButton(
                                                        onPressed: () {
                                                          _showConfirmOrderAlert(context,itemList[ind].oderId,'10',token);
                                                        },
                                                        padding: EdgeInsets.only(left: 30, right: 30),
                                                        child: Text(
                                                          "Cancel",
                                                          style: CustomTextStyle.textFormFieldMedium
                                                              .copyWith(color: Colors.white),
                                                        ),
                                                        color: Colors.red,
                                                        shape: RoundedRectangleBorder(
                                                            borderRadius: BorderRadius.all(Radius.circular(24))),
                                                      ),
                                                      SizedBox(
                                                        width: 15,
                                                      ),
                                                      RaisedButton(
                                                        onPressed: () {
                                                          _showConfirmOrderAlert(context,itemList[ind].oderId,'5',token);
                                                        },
                                                        padding: EdgeInsets.only(left: 30, right: 30),
                                                        child: Text(
                                                          "Accepted",
                                                          style: CustomTextStyle.textFormFieldMedium
                                                              .copyWith(color: Colors.white),
                                                        ),
                                                        color: Colors.green,
                                                        shape: RoundedRectangleBorder(
                                                            borderRadius: BorderRadius.all(Radius.circular(24))),
                                                      )

                                                    ],
                                                  ),
                                                )
                                              ],
                                            ))))),
                          ]
                          )
                      )
                  );
                })
        )
    );

  }
}