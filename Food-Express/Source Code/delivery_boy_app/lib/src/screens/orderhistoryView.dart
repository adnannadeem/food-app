
import 'dart:ffi';
import 'dart:io';

import 'package:deliveryboyapp/src/utils/CustomTextStyle.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:deliveryboyapp/config/api.dart';
import 'package:deliveryboyapp/src/Widget/CircularLoadingWidget.dart';
import 'package:deliveryboyapp/src/Widget/OrderItemWidget.dart';
import 'package:deliveryboyapp/src/shared/colors.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'package:url_launcher/url_launcher.dart';

class OrderViewPage extends StatefulWidget {
  final String orderID;
  final String currency;
  OrderViewPage({Key key, this.orderID,this.currency}) : super(key: key);

  @override
  _OrderHistoryViewPageState createState() {
    return new _OrderHistoryViewPageState();
  }
}

class _OrderHistoryViewPageState extends State<OrderViewPage> {
  GlobalKey<RefreshIndicatorState> refreshKey;
  String api = FoodApi.baseApi;
  final rows = <TableRow>[];
  List _status = List();
  String activeSatus;
  String token;
  Map<String, dynamic> orderView = {"orderId":'',"amount":'',"status_name" :'',"received":'',"sub_total":'',"delivery_charge":'',"status": '',"payment_method":'',"payment_status":'', "oderCode" :'',"Items":[],"payments":[]};
  Map<String, dynamic> customer = {"name":'',"img":null,"phone":'',"address":''};
  Map<String, dynamic> shop = {"name":'',"img":null,"opening_time":'',"closing_time":'',"address":''};

  Future<String> getmyOrder(orderID) async {
    final url = "$api/notification-order/$orderID/show";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    print(resBody);
    if (response.statusCode == 200) {
      var order = json.decode(resBody['data']['misc']);
      setState(() {
        orderView['status_name'] = resBody['data']['status_name'];
        orderView['orderId'] = resBody['data']['id'].toString();
        orderView['sub_total'] = resBody['data']['sub_total'].toString();
        orderView['delivery_charge'] = resBody['data']['delivery_charge'].toString();
        orderView['amount'] = resBody['data']['total'].toString();
        orderView['payment_method'] = resBody['data']['payment_method'].toString();
        orderView['payment_status'] = resBody['data']['payment_status'].toString();
        orderView['status'] = resBody['data']['status'].toString();
        orderView['Items'] = resBody['data']['items'];
        orderView['payments'] = resBody['data']['payments'];
        orderView['oderCode'] = order['order_code'];
        orderView['received'] = resBody['data']['product_received'].toString();
        //customer
        customer['name'] = resBody['data']['customer']['name'];
        customer['phone'] = resBody['data']['mobile'];
        customer['address'] = resBody['data']['address'];
        customer['img'] = resBody['data']['customer']['image'];

        //shop
        shop['name'] = resBody['data']['shop'] !=null?resBody['data']['shop']['name']:'';
        shop['img'] = resBody['data']['shop'] !=null?resBody['data']['shop']['image']:null;
        shop['address'] = resBody['data']['shop'] !=null?resBody['data']['shop']['address']:'';
        shop['opening_time'] = resBody['data']['shop'] !=null?resBody['data']['shop']['opening_time']:'';
        shop['closing_time'] = resBody['data']['shop'] !=null?resBody['data']['shop']['closing_time']:'';

      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }


  Future<Void> orderUpdate(String id, String status) async {
    final url = "$api/notification-order-product-receive/$id/update?product_receive_status=$status";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    print(resBody);
    if (response.statusCode == 200) {
      setState(() {
        refreshList();
      });
    } else {
      throw Exception('Failed to data');
    }

  }

  Future<Void> orderDeliveredUpdate(String id, String status) async {
    final url = "$api/notification-order-status/$id/update?status=$status";
    print(url);
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    var resBody = json.decode(response.body);
    print(resBody);
    if (response.statusCode == 200) {
      setState(() {
        refreshList();
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
          title: Text('Delivery Confirmation'),
          content:Text('Your delivery is not complete. You go to the shop and deposit the money. The shop owner will complete your delivery process.'),
          actions: <Widget>[
            FlatButton(
              child: Text('Dismiss'),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }
  Future<void> _showConfirmOrderAlert(BuildContext context,id ,status,) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Received Confirmation'),
          content:Text('are you sure you have received this products'),
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
                orderUpdate(id, status,);
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }
  Future<void> _showConfirmDeliveredAlert(BuildContext context,id ,status,) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Delivery Confirmation'),
          content:Text('Would you please confirm if you have delivered meals to customer'),
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
                orderDeliveredUpdate(id, status,);
                Navigator.of(context).pop();
              },
            ),
          ],
        );
      },
    );
  }

  Future<Null> refreshList() async {
    setState(() {
      orderView['Items'] = [];
      orderView['payments'] = [];
      activeSatus= null;
      this.getmyOrder(widget.orderID);
    });
  }
  double iconSize = 40;

  @override
  void initState() {
    super.initState();
    token = Provider.of<AuthProvider>(context,listen: false).token;
    this.getmyOrder(widget.orderID);;
  }
  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context).copyWith(dividerColor: Colors.transparent);
    final currency = Provider.of<AuthProvider>(context,listen: false).currency;

    return Scaffold(
        backgroundColor: Colors.indigo[50],
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Theme.of(context).primaryColor,
          title:
          Text('Order Details', textAlign: TextAlign.center),
        ),
      body:
      RefreshIndicator(
        key: refreshKey,
        onRefresh: () async {
        await refreshList();
        },
        child:
        orderView['Items'].isEmpty ? ListView(children:<Widget>[CircularLoadingWidget(height: 500, subtitleText: 'No Orders found',)],):
        Builder(builder: (context) {
          return Column(
            children: <Widget>[
              Expanded(
                child: Container(
                  child:
                  ListView(
                    padding: EdgeInsets.symmetric(vertical: 5),
                    children: <Widget>[
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 15),
                        child: ListTile(
                          dense: true,
                          contentPadding: EdgeInsets.symmetric(vertical: 0),
                          leading: Icon(
                            Icons.store,
                            color: Theme.of(context).hintColor,
                          ),
                          title: Text(
                            'Shop Information',
                            style:CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Color(0xFF575E67),
                                fontSize: 16,
                                fontWeight: FontWeight.bold
                            ),
                          ),
                        ),
                      ),
                      Padding(
                          padding: EdgeInsets.fromLTRB(4, 0, 4, 0),
                          child:  Card(
                              elevation: 0.0,
                              color: Colors.white,
                              child:
                              Container(
                                  padding: const EdgeInsets.fromLTRB(
                                      4, 4, 4, 4),
                                  child: GestureDetector(
                                      child: Column(
                                        mainAxisSize: MainAxisSize.max,
                                        mainAxisAlignment: MainAxisAlignment.start,
                                        children: <Widget>[
                                          Container(
                                            alignment: Alignment.topLeft,
                                            child:
                                            Row(
                                              children: <Widget>[
                                                Image(image: shop['img'] !=null?NetworkImage(shop['img'] ):AssetImage('assets/steak.png'),
                                                  fit: BoxFit.contain,
                                                  height: 90.0,
                                                  width: 90.0,
                                                ),
                                                SizedBox(width: 10.0),
                                                Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: <Widget>[
                                                    Text(
                                                      shop['name'],
                                                      overflow: TextOverflow.fade,
                                                      maxLines: 2,
                                                      softWrap: true,
                                                      style: TextStyle(
                                                          color: Color(0xFF575E67),
                                                          fontFamily: 'Montserrat',
                                                          fontSize: 16.0
                                                      ),
                                                    ),
                                                    SizedBox(height: 5.0),
                                                    Row(
                                                      mainAxisAlignment: MainAxisAlignment.start,
                                                      children: <Widget>[
                                                        Icon(
                                                          Icons.location_on,
                                                          size: 15.0,
                                                          color: Colors.amber.shade500,
                                                        ),
                                                        Text(
                                                           shop['address'],
                                                          style:  TextStyle(
                                                              color: Color(0xFF575E67),
                                                              fontFamily: 'Varela',
                                                              fontSize: 13.0),
                                                          softWrap: false,
                                                          overflow: TextOverflow.fade,
                                                        ),
                                                      ],
                                                    ),
                                                    SizedBox(height: 5.0),
                                                    Container(
                                                      child: Text( ' Opening Time - ' + shop['opening_time'],
                                                        style: TextStyle(
                                                            color: Color(0xFF575E67),
                                                            fontFamily: 'Montserrat',
                                                            fontSize: 13.0
                                                        ),
                                                      ),
                                                    ),
                                                    SizedBox(height: 5.0),
                                                    Container(
                                                      child: Text(' Closing Time - '+shop['closing_time'],
                                                        style: TextStyle(
                                                            color: Color(0xFF575E67),
                                                            fontFamily: 'Montserrat',
                                                            fontSize: 13.0
                                                        ),
                                                      ),
                                                    ),
                                                    SizedBox(height: 6.0),
                                                  ],
                                                )
                                              ],

                                            ),
                                          ),
                                        ],
                                      ))))
                      ),

                      orderView['received'] == '10'?Container(): Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 15),
                        child: ListTile(
                          dense: true,
                          contentPadding: EdgeInsets.symmetric(vertical: 0),
                          leading: Icon(
                            Icons.account_circle,
                            color: Theme.of(context).hintColor,
                          ),
                          title: Text(
                            'Customer Information',
                            style:CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Color(0xFF575E67),
                                fontSize: 16,
                                fontWeight: FontWeight.bold
                            ),
                          ),
                        ),
                      ),
                      orderView['received'] == '10'?Container():
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                        color: Colors.indigo[50],
                        child:
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: <Widget>[
                            Expanded(
                              child: Padding(
                                padding: const EdgeInsets.all(8.0),
                                child:
                                Text(
                                    customer['name'],
                                    overflow: TextOverflow.ellipsis,
                                    style:CustomTextStyle.textFormFieldMedium.copyWith(
                                      color: Color(0xFF575E67),
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold
                                      ),
                                ),
                                )
                            ),
                            SizedBox(width: 10),
                            SizedBox(
                              width: 42,
                              height: 42,
                              child: FlatButton(
                                padding: EdgeInsets.all(0),
                                onPressed: () {},
                                child: Icon(
                                  Icons.account_circle,
                                  color: Colors.white,
                                  size: 24,
                                ),
                                color: Colors.grey,
                                shape: StadiumBorder(),
                              ),
                            ),
                          ],
                        ),
                      ),
                      orderView['received'] == '10'?Container():
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                        color: Colors.indigo[50],
                        child:
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: <Widget>[
                            Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child:
                                  Text(
                                      customer['address'],
                                      overflow: TextOverflow.ellipsis,
                                    style:CustomTextStyle.textFormFieldMedium.copyWith(
                                        color: Color(0xFF575E67),
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold
                                    ),
                                  ),
                                )
                            ),
                            SizedBox(width: 10),
                            SizedBox(
                              width: 42,
                              height: 42,
                              child: FlatButton(
                                padding: EdgeInsets.all(0),
                                onPressed: () {},
                                child: Icon(
                                  Icons.location_on,
                                  color: Colors.white,
                                  size: 24,
                                ),
                                color: Colors.grey,
                                shape: StadiumBorder(),
                              ),
                            ),
                          ],
                        ),
                      ),
                      orderView['received'] == '10'?Container():
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                        margin: const EdgeInsets.symmetric(vertical: 5),
                        color: Colors.indigo[50],
                        child:
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: <Widget>[
                            Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.all(8.0),
                                  child:
                                  Text(
                                      customer['phone'],
                                      overflow: TextOverflow.ellipsis,
                                    style:CustomTextStyle.textFormFieldMedium.copyWith(
                                        color: Color(0xFF575E67),
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold
                                    ),
                                  ),
                                )
                            ),
                            SizedBox(width: 10),
                            SizedBox(
                              width: 42,
                              height: 42,
                              child: FlatButton(
                                padding: EdgeInsets.all(0),
                                onPressed: () {
                                  launch("tel:${customer['phone']}");
                                },
                                child: Icon(
                                  Icons.call,
                                  color: Colors.white,
                                  size: 24,
                                ),
                                color: Theme.of(context).primaryColor,
                                shape: StadiumBorder(),
                              ),
                            ),
                          ],
                        ),
                      ),

                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 15),
                        child: ListTile(
                          dense: true,
                          contentPadding: EdgeInsets.symmetric(vertical: 0),
                          leading: Icon(
                            Icons.fastfood,
                            color: Theme.of(context).hintColor,
                          ),
                          title: Text(
                            'Products Ordered',
                            style:CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Color(0xFF575E67),
                                fontSize: 16,
                                fontWeight: FontWeight.bold
                            ),
                          ),
                        ),
                      ),
                      SizedBox(height: 5),
                      Theme(
                        data: theme,
                        child: Column(
                          children:
                          List.generate(orderView['Items'].length, (index) {
                            return OrderItemWidget(currency:widget.currency,product:orderView['Items'][index]);
                          }),
                        ),
                      ),

                    ],

                  ),
                ),
                flex: 28,
              ),
              Expanded(
                child:
                Container(
                  height: 300,
                  decoration: BoxDecoration(
                      color: Colors.white,
                      border: Border.all(color: Colors.grey.shade200, width: 2),
                      borderRadius: BorderRadius.only(
                          topRight: Radius.circular(16), topLeft: Radius.circular(16))),
                  child: Column(
                    children: <Widget>[
                      SizedBox(height: 10,),
                      Container(
                        margin: EdgeInsets.only(left: 16, right: 16),
                        child: Column(
                          children: <Widget>[
                            Container(
                              padding: EdgeInsets.only(top: 5),
                              child:
                              Row(
                                children: <Widget>[
                                  Text(
                                    'Order Code',
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                  Spacer(
                                    flex: 1,
                                  ),
                                  Text(
                                    orderView['oderCode'],
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: EdgeInsets.only(top: 5),
                              child:
                              Row(
                                children: <Widget>[
                                  Text(
                                    'Pyment Type',
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                  Spacer(
                                    flex: 1,
                                  ),
                                  Text(
                                    orderView['payment_status'].toString() == '10'?'Cash on delivery':'Paid',
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: EdgeInsets.only(top: 5),
                              child:
                              Row(
                                children: <Widget>[
                                  Text(
                                    'Total amount',
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                  Spacer(
                                    flex: 1,
                                  ),
                                  Text(
                                    currency + orderView['amount'],
                                    style: CustomTextStyle.textFormFieldBold
                                        .copyWith(color: Color(0xFF575E67)),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        width: double.infinity,
                        height: 75,
                        margin: EdgeInsets.symmetric(vertical: 3, horizontal: 30),
                        padding: EdgeInsets.all(15.0),
                        child:
                        orderView['received'] == '10'?
                        RaisedButton(
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.all(Radius.circular(24))),
                          onPressed: () {
                            _showConfirmOrderAlert(context,widget.orderID,'5');
                          },
                          child: Text(
                            "Received",
                            style: CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.bold),
                          ),
                          color: Theme.of(context).primaryColor,
                          textColor: Colors.white,
                        ):
                        orderView['payment_status'].toString() == '10'?
                        RaisedButton(
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.all(Radius.circular(24))),
                          onPressed: () {
                            _showAlert(context);
                          },
                          child: Text(
                            "Delivered",
                            style: CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.bold),
                          ),
                          color: Theme.of(context).primaryColor,
                          textColor: Colors.white,
                        ):orderView['status']=='20'?
                        Text('Complete  Delivered',
                          textAlign: TextAlign.center,
                          style: CustomTextStyle.textFormFieldMedium.copyWith(
                              color: Colors.green,
                              fontSize: 18,
                              fontWeight: FontWeight.bold),
                        )
                            :RaisedButton(
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.all(Radius.circular(24))),
                          onPressed: () {
                            _showConfirmDeliveredAlert(context,widget.orderID,'20');
                          },
                          child: Text(
                            "Delivered",
                            style: CustomTextStyle.textFormFieldMedium.copyWith(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.bold),
                          ),
                          color: Theme.of(context).primaryColor,
                          textColor: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),

                flex: 12,
              )
            ],
          );
        }),
      )
    );

  }
}
