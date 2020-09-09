import 'dart:ffi';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:deliveryboyapp/config/api.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'package:deliveryboyapp/src/Widget/CircularLoadingWidget.dart';
import 'package:deliveryboyapp/src/screens/orderhistoryView.dart';
import 'package:deliveryboyapp/src/utils/CustomTextStyle.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';

class OrderHistoryPage extends StatefulWidget {
  OrderHistoryPage({Key key,}) : super(key: key);

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
  final String userName;
  final String userPhone;
  final String userAddress;
  final String shopName;
  final String shopPhone;
  final String shopAddress;
  final String received;
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
        this.received,
        this.userName,
        this.userPhone,
        this.userAddress,
        this.shopName,
        this.shopAddress,
        this.shopPhone,
      this.Items, this.paymentMethod,this.paymentStatus});
}

class _OrderHistoryPageState extends State<OrderHistoryPage> {
  GlobalKey<RefreshIndicatorState> refreshKey;

  String api = FoodApi.baseApi;
  List resOrder = List();
  List<Item> itemList = <Item>[];
  String token;
  String currency;

  Future<String> getmyOrder() async {
    final url = "$api/notification-order/history";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    print(resBody);
    if (response.statusCode == 200) {
      setState(() {
        resOrder = resBody['data'];
        resOrder.forEach((element) {
          var order = json.decode(element['misc']);
          itemList.add(Item(
              deliveryTime: element['updated_at'],
              oderId: '${element['id']}',
              oderCode: '${order['order_code']}',
              oderAmount: '${element['total']}',
              paymentType: element['payment_status'].toString() == '10'?'Cash on delivery':'Paid',
              paymentMethod: '${element['payment_method']}',
              paymentStatus: '${element['payment_status']}',
              address: element['address'],
              status: element['status'].toString(),
              received: element['product_received'].toString(),
              status_name: element['status_name'],
              userName: element['user']['name'],
              userPhone: element['mobile'],
              userAddress: element['address'],
              shopName: element['shop']!=null?element['shop']['name']:'',
              shopAddress: element['shop']!=null?element['shop']['address']:'',
          ));
        });

      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
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
      this.getmyOrder();
    });
  }
  @override
  void initState() {
    super.initState();
     token = Provider.of<AuthProvider>(context,listen: false).token;
    currency = Provider.of<AuthProvider>(context,listen: false).currency;
    getmyOrder();
  }
  @override
  Widget build(BuildContext context) {
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
                                      Container(
                                        alignment: Alignment.topLeft,
                                        child: Row(
                                          children: <Widget>[
                                            itemList[ind].received == '10'? Text(''): Text(
                                              ' '+itemList[ind].userName,
                                              style: TextStyle(
                                                fontSize: 16.0,
                                                fontStyle: FontStyle.normal,
                                                color: Colors.black87,
                                              ),
                                            ),
                                            Spacer(
                                              flex: 1,
                                            ),
                                            Text(
                                              itemList[ind].received == '10'? 'Not Received':  itemList[ind].status == '20'?'Delivered':'Received ',
                                              style: TextStyle(
                                                fontSize: 16.0,
                                                fontStyle: FontStyle.normal,
                                                color: Colors.green,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      SizedBox(height: 3.0,),
                                      itemList[ind].received == '10'?Row():Row(
                                        mainAxisAlignment: MainAxisAlignment.start,
                                        children: <Widget>[
                                          Icon(
                                            Icons.call,
                                            size: 16.0,
                                            color: Colors.amber.shade500,
                                          ),
                                          Text(' '+itemList[ind].userPhone,
                                              style: TextStyle(
                                                  fontSize: 13.0,
                                                  color: Colors.black54)),
                                        ],
                                      ),
                                      itemList[ind].received == '10'?Row():Row(
                                        mainAxisAlignment: MainAxisAlignment.start,
                                        children: <Widget>[
                                          Icon(
                                            Icons.location_on,
                                            size: 16.0,
                                            color: Colors.amber.shade500,
                                          ),
                                          Text(' '+itemList[ind].userAddress,
                                              style: TextStyle(
                                                  fontSize: 13.0,
                                                  color: Colors.black54)),
                                        ],
                                      ),
                                      Container(
                                        margin: EdgeInsets.only(top: 3.0),
                                      ),
                                      Container(
                                        alignment: Alignment.topLeft,
                                        child: Text(
                                          ' To Deliver On :' +
                                              itemList[ind].deliveryTime,
                                          style: TextStyle(
                                              fontSize: 13.0, color: Colors.black54),
                                        ),
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
                                        color: Colors.indigo[100],
                                      ),

                                      Container(
                                        alignment: Alignment.topLeft,
                                        child: Text(
                                          ' '+itemList[ind].shopName,
                                          style: TextStyle(
                                            fontSize: 16.0,
                                            fontStyle: FontStyle.normal,
                                            color: Colors.black87,
                                          ),
                                        ),
                                      ),
                                      SizedBox(height: 3.0,),
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
