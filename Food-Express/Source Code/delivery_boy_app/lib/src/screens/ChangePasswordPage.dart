import 'dart:io';
import 'package:flutter/material.dart';
import 'package:deliveryboyapp/main.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'package:deliveryboyapp/src/Widget/notification_text.dart';
import 'package:deliveryboyapp/src/Widget/styled_flat_button.dart';
import 'package:deliveryboyapp/src/utils/validate.dart';
import 'dart:async';

import 'package:provider/provider.dart';

class ChangePasswordPage extends StatefulWidget {

  ChangePasswordPage({Key key}) : super(key: key);

  @override
  _ChangePasswordPageState createState() => _ChangePasswordPageState();
}

class _ChangePasswordPageState extends State<ChangePasswordPage> {
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String password_current;
  String password;
  String passwordConfirm;
  String message = '';
  Map response = new Map();

  Future<void> submit() async {
    final form = _formKey.currentState;
    print(form.validate());
    if (form.validate()) {
      response = await Provider.of<AuthProvider>(context,listen: false)
          .ChangePassword(password_current,password,passwordConfirm);
      if (response['success']) {
        message = response['message'];
        _showAlert(context,true);
      } else {
        message = response['message'];
        _showAlert(context, false);
      }

    }

  }
  Future<void> _showAlert(BuildContext context, bool) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Change Password'),
          content:Consumer<AuthProvider>(
            builder: (context, provider, child) => provider.notification ?? NotificationText('Password change  Not successful.'),
          ),
          actions: <Widget>[
            FlatButton(
              child: Text('Ok'),
              onPressed: () {
                if(bool){
                  Navigator.push(
                      context, MaterialPageRoute(builder: (context) => MyHomePage(title: 'Profile',tabsIndex: 2,)));
                }else {
                  Navigator.of(context).pop();
                }
              },
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
       return Scaffold(
      appBar: AppBar(
        backgroundColor: Theme.of(context).primaryColor,
        centerTitle: true,
        leading: IconButton(
            icon: Icon(
              Icons.arrow_back,
              color: Colors.white,
            ),
            onPressed: () {
              Navigator.pop(context);
            }),
        title: Text("Change Password"),
      ),
      body: ListView(
          children: <Widget>[
            Form(
              key: _formKey,
              child:Column(
                children:<Widget>[
                  SizedBox(
                    height: 24,
                  ),
                  Container(
                    child:
                    _passwordCurrentWidget(),
                    margin: EdgeInsets.only(left: 12, right: 12, top: 12),
                  ),
                  Container(
                    child:
                    _passwordWidget(),
                    margin: EdgeInsets.only(left: 12, right: 12, top: 12),
                  ),
                  Container(
                    child:
                    _passwordConfirmWidget(),
                    margin: EdgeInsets.only(left: 12, right: 12, top: 12),
                  ),
                  SizedBox(
                    height: 24,
                  ),
                  Container(
                    width: double.infinity,
                    margin: EdgeInsets.only(left: 30, right: 30),
                    child:
                    StyledFlatButton(
                      'Update',
                      onPressed: submit,
                    ),
                  ),
                  SizedBox(
                    height: 30,
                  ),
                ],
              ),
            )
          ]
      ),
    );
  }

  var border = OutlineInputBorder(
      borderRadius: BorderRadius.all(Radius.circular(4)),
      borderSide: BorderSide(width: 1, color: Colors.grey));

  Widget _passwordCurrentWidget() {
    return Column(
      children: <Widget>[
        Container(
          margin: EdgeInsets.symmetric(vertical: 5),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(
                'Current Password',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
              SizedBox(
                height: 5,
              ),
              TextFormField(
                  obscureText: true,
                  decoration: InputDecoration(
                      border: InputBorder.none,
                      fillColor: Color(0xfff3f3f4),
                      filled: true),
                  validator: (value) {
                    password_current = value.trim();
                    return Validate.requiredField(value, 'Current Password is required.');
                  }
              )
            ],
          ),


        )
      ],
    );
  }
  Widget _passwordWidget() {
    return Column(
      children: <Widget>[
        Container(
          margin: EdgeInsets.symmetric(vertical: 5),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(
                'Password',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
              SizedBox(
                height: 5,
              ),
              TextFormField(
                  obscureText: true,
                  decoration: InputDecoration(
                      border: InputBorder.none,
                      fillColor: Color(0xfff3f3f4),
                      filled: true),
                  validator: (value) {
                    password = value.trim();
                    return Validate.requiredField(value, 'Password is required.');
                  }
              )
            ],
          ),


        )
      ],
    );
  }

  Widget _passwordConfirmWidget() {
    return Column(
      children: <Widget>[
        Container(
          margin: EdgeInsets.symmetric(vertical: 5),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(
                'Password Confirm',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
              SizedBox(
                height: 5,
              ),
              TextFormField(
                  obscureText: true,
                  decoration: InputDecoration(
                      border: InputBorder.none,
                      fillColor: Color(0xfff3f3f4),
                      filled: true),
                  validator: (value) {
                    passwordConfirm = value.trim();
                    return Validate.requiredField(value, 'Password confirm is required.');
                  }
              )
            ],
          ),


        )
      ],
    );
  }

}
