import 'dart:ffi';
import 'dart:async';
import 'dart:io';
import 'package:device_info/device_info.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:foodshopapp/models/cartmodel.dart';
import 'package:foodshopapp/providers/auth.dart';
import 'package:foodshopapp/src/Widget/CircularLoadingWidget.dart';
import 'package:foodshopapp/src/Widget/drawer.dart';
import 'package:foodshopapp/src/Widget/loading.dart';
import 'package:foodshopapp/src/screens/ProductPost.dart';
import 'package:foodshopapp/src/screens/ProfilePage.dart';
import 'package:foodshopapp/src/screens/ShopAddPage.dart';
import 'package:foodshopapp/src/screens/ShopProductList.dart';
import 'package:foodshopapp/src/screens/cartpage.dart';
import 'package:foodshopapp/src/screens/loginPage.dart';
import 'package:foodshopapp/src/screens/orderhistory.dart';
import 'package:foodshopapp/src/screens/shopPage.dart';
import 'package:foodshopapp/src/screens/signupPage.dart';
import 'package:provider/provider.dart';
import 'package:scoped_model/scoped_model.dart';
import './src/shared/styles.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
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
      debugShowCheckedModeBanner: false,
       theme: ThemeData(primarySwatch:Colors.green,primaryColor: Color(0xffffffff)),
        initialRoute: '/',
        routes: {
          '/': (context) => Router(),
          '/home': (BuildContext context) => MyHomePage(),
          '/cart': (BuildContext context) => CartPage(),
          '/register': (BuildContext context) => Register(),
          '/login': (BuildContext context) => LoginPage(),
        },
    )
    ),
    );
  }
}

class Router extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, user, child) {
        if(user.status == Status.Uninitialized){
          return Loading();
        }else if(user.status == Status.Unauthenticated){
          return LoginPage();
        }else if(user.status == Status.Authenticated){
          print(user.shopID);
              if(user.shopID !=null){
                return MyHomePage();
              }else {
                return ShopAddPage();
              }
        }else {
          return LoginPage();
        }
      },
    );
  }
}

class MyHomePage extends StatefulWidget {
   String title;
   int tabsIndex;
  MyHomePage({Key key, this.title,this.tabsIndex}) : super(key: key);

  @override
  _MyHomePageState createState() => _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage> {
  int _selectedIndex = 0;
  String _title;
  String _sitename;
  Status authenticated;
  String deviceId;
  String token;
  String api = FoodApi.baseApi;
  FirebaseMessaging firebaseMessaging = new FirebaseMessaging();

  Future<void> setting() async {
    await Provider.of<AuthProvider>(context,listen: false).setting();
  }
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
  Future<Void> deviceTokenUpdate(token) async {
    final url = "$api/device?device_token=$token";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    print(response);
  }
  @override
  void initState() {
    super.initState();
    this.setting();
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
    deviceTokenUpdate(token);
    deviceId =  await _getId();
    setState(() {});
  }
  @override
  Widget build(BuildContext context) {
    _sitename = Provider.of<AuthProvider>(context,listen: false).sitename;
    final _tabs = [
      ShopPage(),
      OrderPage(),
      ProductList(),
      ProfilePage(),
    ];

    return Scaffold(
        backgroundColor: Color(0xffF4F7FA),
        drawer: AppDrawer(),
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Color(0xfffada36),
          title:
          Text(widget.title !=null? widget.title :_sitename!=null ? _sitename:'', textAlign: TextAlign.center),
        ),
        body:SafeArea(
          child:
            _tabs[widget.tabsIndex != null? widget.tabsIndex: _selectedIndex]
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
                icon: Icon(Icons.fastfood),
                title: Text(
                  'My Product',
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
          fixedColor: Color(0xfffada36),
          onTap: _onItemTapped,
        )
    );
  }
  Void _onItemTapped(int index) {
    setState(()  {
      _selectedIndex = index;
      if(index==1){
        widget.tabsIndex =null;
        widget.title ='Order';
      }else if(index==2){
        widget.tabsIndex =null;
        widget.title ='Product';
      }else if(index==3){
        widget.tabsIndex =null;
        widget.title ='Profile';
      }else{
        widget.tabsIndex =null;
        widget.title =_sitename;
      }

    });
  }
}
