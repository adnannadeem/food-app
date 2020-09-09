import 'dart:ffi';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:foodexpress/config/api.dart';
import 'package:foodexpress/providers/auth.dart';
import 'package:foodexpress/src/Widget/CircularLoadingWidget.dart';
import 'package:foodexpress/src/screens/orderhistoryView.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';

class OrderPage extends StatefulWidget {
  OrderPage({Key key,}) : super(key: key);

  @override
  _OrderHistoryPageState createState() {
    return new _OrderHistoryPageState();
  }
}

class Item {
  final String name;
  final String deliveryTime;
  final String oderId;
  final String oderAmount;
  final String status_name;
  final String status;
  final String oderCode;
  final String paymentType;
  final String paymentMethod;
  final String paymentStatus;
  final String address;
  final List Items;

  Item(
      {this.name,
        this.deliveryTime,
        this.oderId,
        this.oderAmount,
        this.paymentType,
        this.address,
        this.oderCode,
        this.status_name,
        this.status,
      this.Items, this.paymentMethod,this.paymentStatus});
}

class _OrderHistoryPageState extends State<OrderPage> {
  GlobalKey<RefreshIndicatorState> refreshKey;

  String api = FoodApi.baseApi;
  List resOrder = List();
  List<Item> itemList = <Item>[];
  Map<String, dynamic> user = {"name" :'', "email" :'', "image" :'',"username":'',"phone":'',"address":''};
  Future<String> getmyProfile(token) async {
    final url = "$api/me";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        user['name'] = resBody['data']['name'];
        user['email'] = resBody['data']['email'];
        user['username'] = resBody['data']['username'];
        user['phone'] = resBody['data']['phone'];
        user['address'] = resBody['data']['address'];
        user['image'] = resBody['data']['image'];
      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }
  Future<String> getmyOrder(token) async {
    final url = "$api/orders";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        resOrder = resBody['data'];
        resOrder.forEach((element) {
          var order = json.decode(element['misc']);
          itemList.add(Item(
              name: 'Admin',
              deliveryTime: element['updated_at_convert'],
              oderId: '${element['id']}',
              oderCode: '${order['order_code']}',
              oderAmount: '${element['total']}',
              paymentType: element['payment_status'].toString() == '10'?'Cash on delivery':'Paid',
              paymentMethod: '${element['payment_method']}',
              paymentStatus: '${element['payment_status']}',
              address: element['address'],
              status: element['status'].toString(),
              status_name: element['status_name'],
              Items: element['items']));
        });

      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }

  Future<Void> orderUpdate(String id, String status,token) async {
    final url = "$api/orders/$id?status=$status";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        itemList.clear();
        this.getmyOrder(token);
        _showAlert(context);
      });
    } else {
      throw Exception('Failed to data');
    }

  }
  Future<void> _showAlert(BuildContext context) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Order Cancel'),
          content: Text('Successfully Updated Order'),
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

  Future<Null> refreshList(String token) async {
    setState(() {
      itemList.clear();
      this.getmyProfile(token);
      this.getmyOrder(token);
    });
  }
  Future<Void> deviceTokenUpdate(token) async {
    SharedPreferences storage = await SharedPreferences.getInstance();
    var deviceToken =  storage.getString('deviceToken');
    final url = "$api/device?device_token=$deviceToken";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
  }
  @override
  void initState() {
    super.initState();
    final token = Provider.of<AuthProvider>(context,listen: false).token;
    getmyProfile(token);
    getmyOrder(token);
    deviceTokenUpdate(token);
  }
  @override
  Widget build(BuildContext context) {
    final token = Provider.of<AuthProvider>(context, listen: false).token;
    final currency = Provider.of<AuthProvider>(context).currency;
    return Scaffold(
      backgroundColor: Color(000),
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
                    onTap: () {
                      Navigator.push(
                        context, MaterialPageRoute(builder: (context) {
                        return new OrderViewPage(orderID: itemList[ind].oderId.toString(),currency:currency);
                      }),
                      );
                    },
             child:
                Column(children: <Widget>[
                  Container(
                      margin: EdgeInsets.only(left: 5.0, right: 5.0, bottom: 5.0),
                      child:
                      Card(
                          elevation: 4.0,
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
                                      Container(
                                        alignment: Alignment.topLeft,
                                        child: Text(
                                          user['name'],
                                          style: TextStyle(
                                            fontSize: 16.0,
                                            fontStyle: FontStyle.normal,
                                            color: Colors.black87,
                                          ),
                                        ),
                                      ),

                                      Container(
                                        margin: EdgeInsets.only(top: 3.0),
                                      ),
                                      Container(
                                        alignment: Alignment.topLeft,
                                        child: Text(
                                          'To Deliver On :' +
                                              itemList[ind].deliveryTime,
                                          style: TextStyle(
                                              fontSize: 13.0, color: Colors.black54),
                                        ),
                                      ),
                                      Divider(
                                        height: 10.0,
                                        color: Colors.amber.shade500,
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
                                                    'Order Amount',
                                                    style: TextStyle(
                                                        fontSize: 13.0,
                                                        color: Colors.black54),
                                                  ),
                                                  Container(
                                                    margin: EdgeInsets.only(top: 3.0),
                                                    child: Text(
                                                      '$currency '+itemList[ind].oderAmount,
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
                                                     itemList[ind].paymentType,
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
                                        color: Colors.amber.shade500,
                                      ),

                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.start,
                                        children: <Widget>[
                                          Icon(
                                            Icons.location_on,
                                            size: 20.0,
                                            color: Colors.amber.shade500,
                                          ),
                                          Text(itemList[ind].address,
                                              style: TextStyle(
                                                  fontSize: 13.0,
                                                  color: Colors.black54)),
                                        ],
                                      ),
                                      Divider(
                                        height: 10.0,
                                        color: Colors.amber.shade500,
                                      ),
                                      Container(
                                          child:_status(token,itemList[ind].oderId,itemList[ind].status,itemList[ind].status_name)
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
  Widget _status(token,oderId, status,status_name) {
    if(status == '5'){
      return FlatButton.icon(
          label: Text('Cancel Order',style: TextStyle(color: Colors.red),),
          icon: const Icon(Icons.highlight_off, size: 18.0,color: Colors.red,),
          onPressed: () {
            // Perform some action
            orderUpdate(oderId,'10',token);
          }
      );
    }
    else{
      if (status == "10") {
        return FlatButton.icon(
            label: Text(status_name,
              style: TextStyle(color: Colors.red),),
            icon: const Icon(Icons.check_circle, size: 18.0,color: Colors.red,),
            onPressed: () {
              // Perform some action
            }
        );
      } else if (status == "15") {
        return FlatButton.icon(
            label: Text(status_name,
              style: TextStyle(color: Colors.green),),
            icon: const Icon(Icons.check_circle, size: 18.0,color: Colors.green,),
            onPressed: () {
              // Perform some action
            }
        );
      } else if (status == "10") {
        return FlatButton.icon(
            label: Text(status_name,
              style: TextStyle(color: Colors.green),),
            icon: const Icon(Icons.check_circle, size: 18.0,color: Colors.green,),
            onPressed: () {
              // Perform some action
            }
        );
      } else {
        return FlatButton.icon(
            label: Text(status_name,
              style: TextStyle(color: Colors.green),),
            icon: const Icon(Icons.check_circle, size: 18.0,color: Colors.green,),
            onPressed: () {
              // Perform some action
            }
        );
      };

    }

  }
}
