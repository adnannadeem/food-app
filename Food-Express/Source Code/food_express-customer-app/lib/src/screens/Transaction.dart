import 'dart:io';

import 'package:flutter/material.dart';
import 'package:foodexpress/config/api.dart';
import 'package:foodexpress/providers/auth.dart';
import 'package:foodexpress/src/Widget/CircularLoadingWidget.dart';
import 'package:foodexpress/src/utils/CustomTextStyle.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';

class Transaction extends StatefulWidget {

  @override
  _Transaction createState() => _Transaction();
}

class _Transaction extends State<Transaction> {
  GlobalKey<RefreshIndicatorState> refreshKey;

  final rows = <TableRow>[];
  String api = FoodApi.baseApi;
  String token;
  String currency;

  Future<String> getTransaction() async {
    final url = "$api/transactions";
    var response = await http.post(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        rows.clear();
        rows.add(
            TableRow( children: [
              Column(
                  children:[
                    Padding(
                        padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                        child: Text('User',
                          style: TextStyle(fontSize: 14),textAlign: TextAlign.center,)),
                  ]),
              Column(
                  children:[
                    Padding(
                        padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                        child: Text('Date',
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
        for (var transaction in resBody['data']) {
          rows.add(TableRow( children: [
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(transaction['user'].toString(),
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(transaction['date'].toString(),
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
            Padding(
                padding: EdgeInsets.fromLTRB(8, 8, 8, 8),
                child: Text(transaction['amount'].toString(),
                  style: TextStyle(fontSize: 12),textAlign: TextAlign.center,)
            ),
          ]),);
        }


        });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }


  @override
  void initState() {
    super.initState();
    token = Provider.of<AuthProvider>(context,listen: false).token;
    currency = Provider.of<AuthProvider>(context,listen: false).currency;
    getTransaction();
  }
  Future<Null> refreshList() async {
    setState(() {
      rows.clear();
      this.getTransaction();
    });
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body:  RefreshIndicator(
    key: refreshKey,
    onRefresh: () async {
    await refreshList();
    },
   child:rows.isEmpty ?  ListView(children:<Widget>[CircularLoadingWidget(height: 500, subtitleText: 'No Transaction found',)],):
      SingleChildScrollView(
        padding: EdgeInsets.symmetric(vertical: 10),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.start,
          mainAxisSize: MainAxisSize.max,
          children: <Widget>[
            SizedBox(height: 15),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              child: ListTile(
                contentPadding: EdgeInsets.symmetric(vertical: 0),
                leading: Icon(
                  Icons.payment,
                  color: Theme.of(context).hintColor,
                ),
                title: Text(
                  'Transaction List',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style:CustomTextStyle.textFormFieldMedium.copyWith(
                      color: Colors.black54,
                      fontSize: 20,
                      fontWeight: FontWeight.bold),
                ),
              ),
            ),
            SizedBox(height: 10),
            Theme(
              data: ThemeData(
                primaryColor: Theme.of(context).accentColor,
              ),
              child:
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
      )
      )
    );
  }
}