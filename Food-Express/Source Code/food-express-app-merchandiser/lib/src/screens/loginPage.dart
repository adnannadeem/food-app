import 'package:flutter/material.dart';
import 'package:foodshopapp/main.dart';
import 'package:foodshopapp/providers/auth.dart';

import 'package:google_fonts/google_fonts.dart';
import 'package:foodshopapp/src/Widget/bezierContainer.dart';
import 'package:foodshopapp/src/screens/signupPage.dart';
import 'package:foodshopapp/src/utils/validate.dart';
import 'package:foodshopapp/src/Widget/notification_text.dart';
import 'package:foodshopapp/src/Widget/styled_flat_button.dart';
import 'package:provider/provider.dart';

import 'otpPage.dart';

class LoginPage extends StatefulWidget {
  LoginPage({Key key, this.title}) : super(key: key);

  final String title;

  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {

  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  String email;
  String password;
  String message = '';
  Future<void> submit() async {
    final form = _formKey.currentState;
    if (form.validate()) {
       var result = await Provider.of<AuthProvider>(context,listen: false).login(email, password);
        if (!result) {
          _showAlert(context);
        }
     }

  }

  Future<void> _showAlert(BuildContext context) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('User Login'),
          content: Consumer<AuthProvider>(
            builder: (context, provider, child) => provider.notification ?? NotificationText(''),
          ),
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
  Future<void> setting() async {
    await Provider.of<AuthProvider>(context,listen: false).setting();
  }
  @override
  void initState() {
    super.initState();
    this.setting();
  }
  @override
  Widget build(BuildContext context) {
    final _sitename = Provider.of<AuthProvider>(context,listen: false).sitename;

    return Scaffold(
      body: SingleChildScrollView(
        child: Container(
            height: MediaQuery.of(context).size.height,
            child: Stack(
              children: <Widget>[
                Container(
                  padding: EdgeInsets.symmetric(horizontal: 20),
                    child:Stack (
                      children: <Widget>[
                        Form(
                          key: _formKey,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: <Widget>[
                              Expanded(
                                flex: 3,
                                child: SizedBox(),
                              ),
                              SizedBox(
                                height: 80,
                              ),
                              _title(_sitename),
                              SizedBox(
                                height: 20,
                              ),
                              _emailWidget(),
                              _passwordWidget(),
                              SizedBox(
                                height: 20,
                              ),
                              StyledFlatButton(
                                'Sign In',
                                onPressed: submit,
                              ),
                              _divider(),
                              StyledFlatButton(
                                'OTP Login',
                                onPressed:  () {
                                  Navigator.push(context,
                                      MaterialPageRoute(builder: (context) => OtpPage()));
                                },
                              ),
                              _divider(),
                              Align(
                                alignment: Alignment.bottomCenter,
                                child: _createAccountLabel(),
                              ),
                              Expanded(
                                flex: 2,
                                child: SizedBox(),
                              ),
                            ],
                          ),
                        )
                      ],
                    )
                  ),
                Positioned(
                    top: -MediaQuery.of(context).size.height * .15,
                    right: -MediaQuery.of(context).size.width * .4,
                    child: BezierContainer())
              ],
            ),
          )
        )
      );

  }

  Widget _backButton() {
    return InkWell(
      onTap: () {
        Navigator.pop(context);
      },
      child: Container(
        padding: EdgeInsets.symmetric(horizontal: 10),
        child: Row(
          children: <Widget>[
            Container(
              padding: EdgeInsets.only(left: 0, top: 10, bottom: 10),
              child: Icon(Icons.keyboard_arrow_left, color: Colors.black),
            ),
            Text('Back',
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.w500))
          ],
        ),
      ),
    );
  }

  Widget _divider() {
    return Container(
      margin: EdgeInsets.symmetric(vertical: 10),
      child: Row(
        children: <Widget>[
          SizedBox(
            width: 20,
          ),
          Expanded(
            child: Padding(
              padding: EdgeInsets.symmetric(horizontal: 10),
              child: Divider(
                thickness: 1,
              ),
            ),
          ),
          Text('or'),
          Expanded(
            child: Padding(
              padding: EdgeInsets.symmetric(horizontal: 10),
              child: Divider(
                thickness: 1,
              ),
            ),
          ),
          SizedBox(
            width: 20,
          ),
        ],
      ),
    );
  }

  Widget _createAccountLabel() {
    return Container(
      margin: EdgeInsets.symmetric(vertical: 20),
      alignment: Alignment.bottomCenter,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: <Widget>[
          Text(
            'Don\'t have an account ?',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
          ),
          SizedBox(
            width: 10,
          ),
          InkWell(
            onTap: () {
              Navigator.push(context,
                  MaterialPageRoute(builder: (context) => SignUpPage()));
            },
            child: Text(
              'Register',
              style: TextStyle(
                  color: Color(0xfffada36),
                  fontSize: 13,
                  fontWeight: FontWeight.w600),
            ),
          )
        ],
      ),
    );
  }

  Widget _title(_sitename) {
    return RichText(
      textAlign: TextAlign.center,
      text: TextSpan(
        text: _sitename !=null ? _sitename:'',
        style: GoogleFonts.portLligatSans(
          textStyle: Theme.of(context).textTheme.display1,
          fontSize: 30,
          fontWeight: FontWeight.w700,
          color: Color(0xffe46b10),
        ),
      ),
    );
  }

  Widget _passwordWidget() {
    return Column(
      children: <Widget>[
        Container(
          margin: EdgeInsets.symmetric(vertical: 10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(
                'Password',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
              SizedBox(
                height: 10,
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
  Widget _emailWidget() {
    return Column(
      children: <Widget>[
        Container(
          margin: EdgeInsets.symmetric(vertical: 10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Text(
                'Email',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
              SizedBox(
                height: 10,
              ),
              TextFormField(
                  obscureText: false,
                  decoration: InputDecoration(
                      border: InputBorder.none,
                      fillColor: Color(0xfff3f3f4),
                      filled: true),
                  validator: (value) {
                    email = value.trim();
                    return Validate.requiredField(value, 'Email is required.');
                  }
              )
            ],
          ),


        )
      ],
    );
  }

}
