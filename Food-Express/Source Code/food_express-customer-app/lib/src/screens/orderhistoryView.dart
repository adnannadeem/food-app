
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:foodexpress/config/api.dart';
import 'package:foodexpress/src/Widget/CircularLoadingWidget.dart';
import 'package:foodexpress/src/Widget/OrderItemWidget.dart';
import 'package:foodexpress/src/screens/paymentType.dart';
import 'package:foodexpress/src/shared/colors.dart';
import 'package:foodexpress/src/utils/CustomTextStyle.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:foodexpress/providers/auth.dart';

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
  Map<String, dynamic> orderView = {"orderId":'',"amount":'',"status_name" :'',"status": '',"payment_method":'',"payment_status":'', "oderCode" :'',"Items":[],"payments":[]};

  Future<String> getmyOrder(orderID,token) async {
    final url = "$api/orders/$orderID/show";
    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      var order = json.decode(resBody['data']['misc']);
      setState(() {
        orderView['status_name'] = resBody['data']['status_name'];
        orderView['orderId'] = resBody['data']['id'].toString();
        orderView['amount'] = resBody['data']['total'].toString();
        orderView['payment_method'] = resBody['data']['payment_method'].toString();
        orderView['payment_status'] = resBody['data']['payment_status'].toString();
        orderView['status'] = resBody['data']['status'].toString();
        orderView['Items'] = resBody['data']['items'];
        orderView['payments'] = resBody['data']['payments'];
        orderView['oderCode'] = order['order_code'];
        rows.clear();
        rows.add(
            TableRow( children: [
          Column(
              children:[
                Padding(
                    padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                    child: Text('Paid at',
                      style: TextStyle(fontSize: 14),textAlign: TextAlign.center,)),
              ]),
          Column(
              children:[
                Padding(
                    padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                    child: Text('Method',
                      style: TextStyle(fontSize: 14),textAlign: TextAlign.center,)),
              ]),
          Column(
              children:[
                Padding(
                    padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                    child: Text('Amount',
                      style: TextStyle(fontSize: 14),textAlign: TextAlign.center,)),
              ]),
        ]));
           var paidAmount = 0;
        for (var payment in orderView['payments']) {
          paidAmount+=payment['amount'];
          rows.add(TableRow( children: [
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(payment['updated_at'].toString(),
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(payment['meta']['payment_method']== '10'?'Stripe':payment['meta']['payment_method']== '20'?'Razorpay':'Paypel',
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(widget.currency+' ${payment['amount']}',
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
          ]),);
        }
        rows.add(TableRow( children: [
          Padding(
              padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
              child: Text('',
                style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
          ),
          Padding(
              padding: EdgeInsets.fromLTRB(0, 8, 8,0),
              child: Text('Paid amount ',
                style: TextStyle(fontSize: 13),textAlign: TextAlign.right,)
          ),
          Padding(
              padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
              child: Text(widget.currency+' ${paidAmount}',
                style: TextStyle(fontSize: 13),textAlign: TextAlign.center,)
          ),
        ]),);
        rows.add(TableRow( children: [
          Padding(
              padding: EdgeInsets.fromLTRB(0, 8, 8, 0),
              child: Text('',
                style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
          ),
          Padding(
              padding: EdgeInsets.fromLTRB(8, 8, 8,0),
              child: Text('Due amount',
                style: TextStyle(fontSize: 13),textAlign: TextAlign.right,)
          ),
          Padding(
              padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
              child: Text(widget.currency+' ${resBody['data']['total']- paidAmount}',
                style: TextStyle(fontSize: 13),textAlign: TextAlign.center,)
          ),
        ]));
      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }
  Future<Null> refreshList() async {
    final _token = Provider.of<AuthProvider>(context,listen: false).token;
    setState(() {
      orderView['Items'] = [];
      orderView['payments'] = [];
      this.getmyOrder(widget.orderID,_token);
    });
  }
  double iconSize = 40;

  @override
  void initState() {
    super.initState();
    final _token = Provider.of<AuthProvider>(context,listen: false).token;
    this.getmyOrder(widget.orderID,_token);
  }
  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context).copyWith(dividerColor: Colors.transparent);

    return Scaffold(
        backgroundColor: Color(0xffF4F7FA),
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Color(0xff44c662),
          title:
          Text('Order view', textAlign: TextAlign.center),
        ),
      body:
      RefreshIndicator(
        key: refreshKey,
        onRefresh: () async {
        await refreshList();
        },
        child: orderView['Items'].isEmpty ? ListView(children:<Widget>[CircularLoadingWidget(height: 500, subtitleText: 'No Orders found',)],):
        Builder(builder: (context) {
          return Column(
            children: <Widget>[
              Expanded(
                child:
                Container(
                  child:
                  ListView(
                    padding: EdgeInsets.symmetric(vertical: 10),
                    children: <Widget>[
                      Theme(
                        data: theme,
                        child: ExpansionTile(
                          initiallyExpanded: true,
                          title: Row(
                            children: <Widget>[
                              Expanded(child: Text(orderView['oderCode']!=null ? orderView['oderCode']:'')),
                              Text(
                                orderView['status_name']!=null ? orderView['status_name']:'',
                                style: Theme.of(context).textTheme.caption,
                              ),
                            ],
                          ),
                          children: List.generate(orderView['Items'].length, (index) {
                            return OrderItemWidget(currency:widget.currency,product:orderView['Items'][index]);
                          }),
                        ),
                      ),
                      SizedBox(height: 20),
                      Theme(
                        data: ThemeData(
                          primaryColor: Theme.of(context).accentColor,
                        ),
                        child: Stepper(
                          physics: ClampingScrollPhysics(),
                          controlsBuilder: (BuildContext context,
                              {VoidCallback onStepContinue, VoidCallback onStepCancel}) {
                            return SizedBox(height: 0.0);
                          },
                          steps: getTrackingSteps(context,orderView['status_name'],orderView['status']),
                          currentStep: 1 - 1,
                        ),
                      ),
                      SizedBox(height: 20),
                      Padding(
                          padding: EdgeInsets.fromLTRB(15, 4, 4, 8),
                          child: Text(
                            'Payment details',
                            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold,color: primaryColor),
                          )
                      ),
                      Padding(
                          padding: EdgeInsets.fromLTRB(15, 4, 4, 8),
                          child:Text(
                            'Payment status -'+ orderView['payment_status'] == '5' ? 'Paid':'Unpaid',
                            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                          )
                      ),
                      SizedBox(height: 20),
                      Theme(
                        data: ThemeData(
                          primaryColor: Theme.of(context).accentColor,
                        ),
                        child:orderView['payments'].isEmpty ?
                          Padding(
                          padding: EdgeInsets.fromLTRB(15, 4, 4, 8),
                          child:Text(
                          'You havent make any payment yet.',
                          style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                          )
                          )
                         :
                          Center(
                          child: Column(
                              children: <Widget>[
                                 Container(
                          margin: EdgeInsets.all(10),
                          child:
                          Table(
                            border: new TableBorder(
                                horizontalInside: new BorderSide(color: Colors.grey[500], width: 0.8)
                            ),
                            columnWidths: {0: FractionColumnWidth(.4), 1: FractionColumnWidth(.3), 2: FractionColumnWidth(.4)},

                          children: rows,
                        ),
                        ),
                       ])),
                      ),

                    ],

                  ),
                ),
                flex: 90,
              ),
              Expanded(
                child: orderView['payment_status'] == '5' ? Container():
                Container(
                  width: double.infinity,
                  margin: EdgeInsets.symmetric(vertical: 8, horizontal: 12),
                  child: RaisedButton(
                    onPressed: () {
                      Navigator.push(
                          context, MaterialPageRoute(builder: (context) => PaymentMethodsPage(amount: orderView['amount'],orderID: orderView['orderId'],)));
                    },
                    child: Text(
                      "Pay Now",
                      style: CustomTextStyle.textFormFieldMedium.copyWith(
                          color: Colors.white,
                          fontSize: 14,
                          fontWeight: FontWeight.bold),
                    ),
                    color: Colors.green,
                    textColor: Colors.white,
                  ),
                ),
                flex: 10,
              )
            ],
          );
        }),
      )
    );

  }
  List<Step> getTrackingSteps(BuildContext context,statusName,status) {
    List<Step> _orderStatusSteps = [];
    if (status == '10' || status == '12'){
      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          status == '10'?'Cancel':'Reject',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse(status == '10'?'10':'12')),
      ));
    }else {
      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          'Pending',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse('5')),
      ));
      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          'Accept',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse('14')),
      ));

      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          'Process',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse('15')),
      ));
      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          'On the Way',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse('17')),
      ));
      _orderStatusSteps.add(Step(
        state: StepState.complete,
        title: Text(
          'Completed',
          style: Theme.of(context).textTheme.subhead,
        ),
        content: SizedBox(
            width: double.infinity,
            child: Text(
              '',
            )),
        isActive: (int.tryParse(status)) >= (int.tryParse('20')),
      ));
    }
    return _orderStatusSteps;
  }
}
