import 'dart:convert';
import 'dart:ffi';
import 'dart:async';
import 'dart:io';
import 'package:device_info/device_info.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:deliveryboyapp/models/cartmodel.dart';
import 'package:deliveryboyapp/providers/auth.dart';
import 'package:deliveryboyapp/src/Widget/drawer.dart';
import 'package:deliveryboyapp/src/Widget/loading.dart';
import 'package:deliveryboyapp/src/screens/ProfilePage.dart';
import 'package:deliveryboyapp/src/screens/ShopProductList.dart';
import 'package:deliveryboyapp/src/screens/cartpage.dart';
import 'package:deliveryboyapp/src/screens/loginPage.dart';
import 'package:deliveryboyapp/src/screens/orderhistory.dart';
import 'package:deliveryboyapp/src/screens/OrdersPage.dart';
import 'package:deliveryboyapp/src/screens/signupPage.dart';
import 'package:provider/provider.dart';
import 'package:scoped_model/scoped_model.dart';
import './src/shared/styles.dart';
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
       theme: ThemeData(primaryColor: Color(0xFFea5c44).withOpacity(1)),
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
          return MyHomePage();
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
  Future<Void> deviceTokenUpdate(deviceToken) async {
    final url = "$api/device?device_token=$deviceToken";
    final response = await http.put(url, headers: {HttpHeaders.acceptHeader: "application/json",HttpHeaders.authorizationHeader: 'Bearer $token'});
    print('token update');
    print(jsonDecode(response.body));
  }
  Future<void> _showAlert(BuildContext context,msg) {
    return showDialog<void>(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(msg['title']),
          content: Text(msg['body']),
          actions: <Widget>[
            FlatButton(
              child: Text('Ok'),
              onPressed: () {
                Navigator.of(context).push(MaterialPageRoute(
                  builder: (context) => MyHomePage(tabsIndex:0),
                ));
              },
            ),
          ],
        );
      },
    );
  }

  @override
  void initState() {
    super.initState();
    this.setting();
    firebaseMessaging.configure(
      onLaunch: (Map<String, dynamic> msg) {
        print(" onLaunch called ${(msg)}");
      },
      onResume: (Map<String, dynamic> msg) {
        print(" onResume called ${(msg)}");
      },
      onMessage: (Map<String, dynamic> msg) {
        _showAlert(context,msg['notification']);
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
    token = Provider.of<AuthProvider>(context,listen: false).token;
    final _tabs = [
      OrderPage(),
      OrderHistoryPage(),
      ProfilePage(),
    ];

    return Scaffold(
        backgroundColor: Color(0xffF4F7FA),
        drawer: AppDrawer(),
        appBar: AppBar(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Theme.of(context).primaryColor,
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
                icon: Icon(Icons.fastfood),
                title: Text(
                  'Orders',
                  style: tabLinkStyle,
                )),
            BottomNavigationBarItem(
                icon: Icon(Icons.shopping_basket),
                title: Text(
                  'Order History',
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
          fixedColor: Theme.of(context).primaryColor,
          onTap: _onItemTapped,
        )
    );
  }
  Void _onItemTapped(int index) {
    setState(()  {
      _selectedIndex = index;
      if(index==1){
        widget.tabsIndex =null;
        widget.title ='Order History';
      }else if(index==2){
        widget.tabsIndex =null;
        widget.title ='Profile';
      }else{
        widget.tabsIndex =null;
        widget.title =_sitename;
      }

    });
  }
}
