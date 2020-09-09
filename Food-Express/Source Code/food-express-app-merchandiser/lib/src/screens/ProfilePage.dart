import 'dart:io';
import 'package:flutter/material.dart';
import 'package:foodshopapp/config/api.dart';
import 'package:foodshopapp/main.dart';
import 'package:foodshopapp/providers/auth.dart';
import 'package:foodshopapp/src/Widget/CircularLoadingWidget.dart';
import 'package:foodshopapp/src/screens/ChangePasswordPage.dart';
import 'package:foodshopapp/src/screens/EditProfilePage.dart';
import 'package:foodshopapp/src/utils/CustomTextStyle.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';

class ProfilePage extends StatefulWidget {
  ProfilePage({Key key,}) : super(key: key);
  @override
  _ProfilePageState createState() {
    return new _ProfilePageState();
  }
}

class _ProfilePageState extends State<ProfilePage> {
  GlobalKey<RefreshIndicatorState> refreshKey;

  String api = FoodApi.baseApi;
  Map<String, dynamic> result = {"name" :'', "email" :'',"balance":'', "image" :'',"username":'',"phone":' ',"address":' '};
  Future<String> getmyProfile(token) async {
    final url = "$api/me";

    var response = await http.get(url,headers: {HttpHeaders.authorizationHeader: 'Bearer $token',HttpHeaders.contentTypeHeader: "application/json; charset=utf-8"});
    var resBody = json.decode(response.body);
    if (response.statusCode == 200) {
      setState(() {
        result['name'] = resBody['data']['name'];
        result['email'] = resBody['data']['email'];
        result['username'] = resBody['data']['username'];
        result['phone'] = resBody['data']['phone'];
        result['address'] = resBody['data']['address'];
        result['image'] = resBody['data']['image'];
        result['balance'] = resBody['data']['balance'] !=null ?resBody['data']['balance']:'00';
      });
    } else {
      throw Exception('Failed to data');
    }
    return "Sucess";
  }
  Future<Null> refreshList(String token) async {
    setState(() {
      getmyProfile(token);
    });
  }
  @override
  void initState() {
    super.initState();
    final token = Provider.of<AuthProvider>(context,listen: false).token;
    getmyProfile(token);
  }

  @override
  Widget build(BuildContext context) {
    final token = Provider.of<AuthProvider>(context,listen: false).token;
    final currency = Provider.of<AuthProvider>(context,listen: false).currency;
    return Scaffold(
      backgroundColor: Colors.grey.shade200,
      resizeToAvoidBottomPadding: true,
      body: RefreshIndicator(
      key: refreshKey,
      onRefresh: () async {
      await refreshList(token);
    },
    child:        result['name']== '' ? CircularLoadingWidget(height: 500,subtitleText: 'profile not found',):
    Container(
      child: Stack(
      children: <Widget>[
        Container(
        height: 180,
        width: double.infinity,
        decoration: BoxDecoration(
            color: Colors.yellow.withOpacity(0.5),
            borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(10),
                bottomRight: Radius.circular(10))),
        child: Stack(
          children: <Widget>[
            Positioned(
              child: Container(
                width: 200,
                height: 200,
                decoration: BoxDecoration(
                    color: Colors.yellow, shape: BoxShape.circle),
              ),
              top: -40,
              left: -40,
            ),
            Positioned(
              child: Container(
                width: 300,
                height: 260,
                decoration: BoxDecoration(
                    color: Colors.yellow.withOpacity(0.5),
                    shape: BoxShape.circle),
              ),
              top: -40,
              left: -40,
            ),
            Positioned(
              child: Align(
                child: Container(
                  width: 400,
                  height: 260,
                  decoration: BoxDecoration(
                      color: Colors.yellow.withOpacity(0.5),
                      shape: BoxShape.circle),
                ),
              ),
              top: -40,
              left: -40,
            ),
          ],
        ),
      ),
        Column(
        mainAxisSize: MainAxisSize.max,
        children: <Widget>[
          Expanded(
            child: Container(),
            flex: 10,
          ),
          Expanded(
            child:
            Container(
              height:  double.infinity,
              child: Stack(
                children: <Widget>[
                  Container(
                    height:  double.infinity,
                    child: Card(
                      margin:
                      EdgeInsets.only(top: 50, left: 16, right: 16,),
                      color: Colors.white,
                      shape: RoundedRectangleBorder(
                          borderRadius:
                          BorderRadius.all(Radius.circular(16))),
                      child: Column(
                        children: <Widget>[
                          Container(
                            margin: EdgeInsets.only(
                                left: 8, top: 8, right: 8, bottom: 8),
                            child: Row(
                              mainAxisAlignment:
                              MainAxisAlignment.spaceBetween,
                              children: <Widget>[
                                ClipOval(
                                  child: Material(
                                    color: Colors.black12, // button color
                                    child: InkWell(
                                      splashColor: Colors.red, // inkwell color
                                      child: SizedBox(width: 50, height: 50, child: Icon(Icons.vpn_key)),
                                      onTap: () {
                                        Navigator.push(
                                            context,
                                            new MaterialPageRoute(
                                                builder: (context) =>
                                                    ChangePasswordPage()));
                                      },
                                    ),
                                  ),
                                ),
                                ClipOval(
                                  child: Material(
                                    color: Colors.black12, // button color
                                    child: InkWell(
                                      splashColor: Colors.red, // inkwell color
                                      child: SizedBox(width: 50, height: 50, child: Icon(Icons.edit)),
                                      onTap: () {
                                        Navigator.push(
                                            context,
                                            new MaterialPageRoute(
                                                builder: (context) =>
                                                    EditProfilePage(userdata: result,)));
                                      },
                                    ),
                                  ),
                                ),

                              ],
                            ),
                          ),
                          SizedBox(
                            height: 8,
                          ),
                          Text(
                            result['name'],
                            style: CustomTextStyle.textFormFieldBlack
                                .copyWith(
                                color: Colors.black,
                                fontSize: 16,
                                fontWeight: FontWeight.w900),
                          ),
                          SizedBox(
                            height: 10,
                          ),
                          Text(
                            'Balance - $currency'+ result['balance'].toString(),
                            style: CustomTextStyle.textFormFieldMedium
                                .copyWith(
                                color: Colors.grey.shade700,
                                fontSize: 14),
                          ),
                          SizedBox(
                            height: 16,
                          ),
                          Container(
                            height: 2,
                            width: double.infinity,
                            color: Colors.grey.shade200,
                          ),
                          nameItem(result['name']),
                          emailItem(result['email']),
                          usernameItem(result['username']),
                          phoneItem(result['phone']),
                          addressItem(result['address']),
                          SizedBox(
                            height: 16,
                          ),
                        ],
                      ),
                    ),
                  ),
                  Align(
                    alignment: Alignment.topCenter,
                    child: Container(
                      decoration: BoxDecoration(
                          border: Border.all(
                              color: Colors.grey.shade400, width: 2),
                          shape: BoxShape.circle,
                          image: DecorationImage(
                              image: result['image'] !=null ? NetworkImage(result['image']): AssetImage('assets/steak.png'),
                              fit: BoxFit.contain)),
                      width: 100,
                      height: 100,
                    ),
                  ),
                ],
              ),
            ),
            flex: 400,
          ),
          Expanded(
            child: Container(),
            flex: 10,
          )
        ],
      )
      ],
    ),
    ),

    )
    );
  }


  nameItem(name) {
    return InkWell(
      splashColor: Colors.teal.shade200,
      child: Container(
        margin: EdgeInsets.only(left: 16, right: 12),
        padding: EdgeInsets.only(top: 12, bottom: 12),
        child: Row(
          children: <Widget>[
            Text(
              'Name',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
            Spacer(
              flex: 1,
            ),
            Text(
              name,
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
          ],
        ),
      ),
    );
  }
  emailItem(email) {
    return InkWell(
      splashColor: Colors.teal.shade200,
      child: Container(
        margin: EdgeInsets.only(left: 16, right: 12),
        padding: EdgeInsets.only(top: 12, bottom: 12),
        child: Row(
          children: <Widget>[
            Text(
              'Email',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
            Spacer(
              flex: 1,
            ),
            Text(
              email !=null?email:'',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
          ],
        ),
      ),
    );
  }
  usernameItem(username) {
    return InkWell(
      splashColor: Colors.teal.shade200,
      child: Container(
        margin: EdgeInsets.only(left: 16, right: 12),
        padding: EdgeInsets.only(top: 12, bottom: 12),
        child: Row(
          children: <Widget>[
            Text(
              'Username',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
            Spacer(
              flex: 1,
            ),
            Text(
              username !=null?username:'',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
          ],
        ),
      ),
    );
  }
  phoneItem(phone) {
    return InkWell(
      splashColor: Colors.teal.shade200,
      child: Container(
        margin: EdgeInsets.only(left: 16, right: 12),
        padding: EdgeInsets.only(top: 12, bottom: 12),
        child: Row(
          children: <Widget>[
            Text(
              'Phone',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
            Spacer(
              flex: 1,
            ),
            Text(
              phone,
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
          ],
        ),
      ),
    );
  }
  addressItem(address) {
    return InkWell(
      splashColor: Colors.teal.shade200,
      child: Container(
        margin: EdgeInsets.only(left: 16, right: 12),
        padding: EdgeInsets.only(top: 12, bottom: 12),
        child: Row(
          children: <Widget>[
            Text(
              'Address',
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
            Spacer(
              flex: 1,
            ),
            Text(
              address !=null ?address: '' ,
              style: CustomTextStyle.textFormFieldBold
                  .copyWith(color: Colors.grey.shade500),
            ),
          ],
        ),
      ),
    );
  }

}
