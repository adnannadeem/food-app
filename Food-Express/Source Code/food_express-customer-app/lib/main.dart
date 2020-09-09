import 'dart:ffi';

import 'package:device_info/device_info.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:foodexpress/models/cartmodel.dart';
import 'package:foodexpress/providers/auth.dart';
import 'package:foodexpress/src/screens/Category.dart';
import 'package:foodexpress/src/screens/ProfilePage.dart';
import 'package:foodexpress/src/screens/Transaction.dart';
import 'package:foodexpress/src/screens/cartpage.dart';
import 'package:foodexpress/src/screens/loginPage.dart';
import 'package:foodexpress/src/screens/orderhistory.dart';
import 'package:foodexpress/src/screens/shopPage.dart';
import 'package:foodexpress/src/screens/signupPage.dart';
import 'package:provider/provider.dart';
import 'package:scoped_model/scoped_model.dart';
import 'package:shared_preferences/shared_preferences.dart';
import './src/shared/styles.dart';
import './src/shared/fryo_icons.dart';
import 'config/api.dart';

void main() => runApp(MyApp());

class MyApp extends StatelessWidget {
  // This widget is the root of your application.
  final AuthProvider _auth = AuthProvider();

  @override
  Widget build(BuildContext context) {

    return MultiProvider(
        providers: [
        ChangeNotifierProvider<AuthProvider>.value(value: _auth),
      ],
      child:ScopedModel(
        model: CartModel(),
      child:MaterialApp(
      title: 'Food Exprese',
      theme: ThemeData(primarySwatch: Colors.green,
      ),
        routes: {
          '/': (BuildContext context) => MyHomePage(),
          '/home': (BuildContext context) => MyHomePage(),
          '/category': (BuildContext context) => Category(shopID: '1',),
          '/cart': (BuildContext context) => CartPage(),
          '/register': (BuildContext context) => Register(),
          '/login': (BuildContext context) => LoginPage(),
        },
    )
    ),
    );
  }
}

class MyHomePage extends StatefulWidget {
   String title;
   int tabsIndex;
  MyHomePage({Key key, this.title,this.tabsIndex}) : super(key: key);

  @override
  _MyHomePageState createState() => _MyHomePageState(title);
}

class _MyHomePageState extends State<MyHomePage> {
  _MyHomePageState(this.authenticated);
  @override
  int _selectedIndex = 0;
  String _title;
  String _sitename;
  var authenticated;
  String token;
  String deviceId;
  String api = FoodApi.baseApi;
  FirebaseMessaging firebaseMessaging = new FirebaseMessaging();


  Future<String> _getId() async {
    DeviceInfoPlugin deviceInfo = DeviceInfoPlugin();
    if (Theme.of(context).platform == TargetPlatform.iOS) {
      IosDeviceInfo iosDeviceInfo = await deviceInfo.iosInfo;
      return iosDeviceInfo.identifierForVendor; // unique ID on iOS
    } else {
      AndroidDeviceInfo androidDeviceInfo = await deviceInfo.androidInfo;
      return androidDeviceInfo.device; // unique ID on Android
    }
  }

  @override
  void initState() {
    super.initState();
    token = Provider.of<AuthProvider>(context,listen: false).token;
    firebaseMessaging.configure(
      onLaunch: (Map<String, dynamic> msg) {
        print(" onLaunch called ${(msg)}");
      },
      onResume: (Map<String, dynamic> msg) {
        print(" onResume called ${(msg)}");
      },
      onMessage: (Map<String, dynamic> msg) {
        print(" onMessage called ${(msg)}");
      },
    );
    firebaseMessaging.requestNotificationPermissions(
        const IosNotificationSettings(sound: true, alert: true, badge: true));
    firebaseMessaging.onIosSettingsRegistered
        .listen((IosNotificationSettings setting) {
      print('IOS Setting Registed');
    });
    firebaseMessaging.getToken().then((token) {
      update(token);
    });
  }

  update(String token) async {
    deviceId =  await _getId();
    SharedPreferences storage = await SharedPreferences.getInstance();
      await storage.setString('deviceToken', token);
      await storage.setString('deviceId', deviceId);
      print(token);
      setState(() {});
  }
  @override
  Widget build(BuildContext context) {
    authenticated = Provider.of<AuthProvider>(context).status;
     token = Provider.of<AuthProvider>(context,listen: false).token;
    _sitename = Provider.of<AuthProvider>(context).sitename;
    final _tabs = [
      ShopPage(),
      OrderPage(),
      Transaction(),
      ProfilePage(),
    ];

    return Scaffold(
        backgroundColor: Color(0xffF4F7FA),
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Color(0xff44c662),
          title:
          Text(widget.title !=null? widget.title :_title != null ? _title:_sitename != null?_sitename:'', textAlign: TextAlign.center),
          actions: <Widget>[
            authenticated == Status.Authenticated ? Text(''):
            IconButton(
              padding: EdgeInsets.all(0),
              onPressed: () { Navigator.push(
                  context, MaterialPageRoute(builder: (context) => LoginPage()));},
              iconSize: 21,
              icon: Icon(Icons.exit_to_app),
            ),
          ],
        ),
        body:SafeArea(
          child:
            _tabs[widget.tabsIndex != null? widget.tabsIndex:_selectedIndex]
        ),
        bottomNavigationBar: BottomNavigationBar(
          items: <BottomNavigationBarItem>[
            BottomNavigationBarItem(
                icon: Icon(Fryo.shop),
                title: Text(
                  'Store',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.cart),
                title: Text(
                  'My Order',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.list),
                title: Text(
                  'Transaction',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Fryo.user_1),
                title: Text(
                  'Profile',
                  style: tabLinkStyle,
                )),
          ],
          currentIndex: widget.tabsIndex != null ? widget.tabsIndex :_selectedIndex,
          type: BottomNavigationBarType.fixed,
          fixedColor: Colors.green[600],
          onTap: _onItemTapped,
        )
    );
  }
  Void _onItemTapped(int index) {
    setState(()  {
      widget.tabsIndex = null;
      widget.title = null;
      print(index);
      ScopedModel.of<CartModel>(context, rebuildOnChange: true).clearCart();
      if(index == 1){
        if (authenticated == Status.Authenticated) {
          _selectedIndex =1;
          _title = 'My Order';
        } else {
          Navigator.push(context,
              new MaterialPageRoute(builder: (context) => LoginPage()));
        }
      }else if(index == 2){
        if (authenticated == Status.Authenticated) {
          _selectedIndex =2;
          _title = 'Transaction';
        } else {
          Navigator.push(context,
              new MaterialPageRoute(builder: (context) => LoginPage()));
        }
      }else if(index == 3){
        if (authenticated == Status.Authenticated) {
          _selectedIndex =3;
          _title = 'Profile';
        } else {
          Navigator.push(context,
              new MaterialPageRoute(builder: (context) => LoginPage()));
        }
      }else {
        _selectedIndex =0;
        _title = _sitename;
      }
    });
  }
}
