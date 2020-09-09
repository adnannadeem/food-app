
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:foodshopapp/main.dart';
import 'package:foodshopapp/src/screens/ProductRequestList.dart';
import 'package:foodshopapp/src/screens/ProductRequstPost.dart';
import 'package:foodshopapp/src/screens/Shopdetails.dart';
import 'package:foodshopapp/src/screens/Transaction.dart';
import 'package:foodshopapp/src/screens/loginPage.dart';
import 'package:foodshopapp/src/screens/salesReport.dart';
import 'package:provider/provider.dart';
import 'package:foodshopapp/providers/auth.dart';

class AppDrawer extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final shop =   Provider.of<AuthProvider>(context,listen: false).shopName;
    final opening =   Provider.of<AuthProvider>(context,listen: false).openingTime;
    final closing =   Provider.of<AuthProvider>(context,listen: false).closingTime;
    final charge =   Provider.of<AuthProvider>(context,listen: false).deliveryCharge;
    final address =   Provider.of<AuthProvider>(context,listen: false).shopAddress;
    final shopImg =   Provider.of<AuthProvider>(context,listen: false).shopImg;

    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: <Widget>[
          Container(
              height: 300.0,
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    SizedBox(height: 30.0),
                    Container(
                        margin: EdgeInsets.all(10.0),
                        width: 100.0,
                        height: 100.0,
                        decoration: new BoxDecoration(
                            shape: BoxShape.circle,
                            image: new DecorationImage(
                                fit: BoxFit.cover,
                                image: shopImg !=null ? NetworkImage(shopImg) :ExactAssetImage('assets/images/profile.png')
                            )
                        )),
                    Container(
                      margin: EdgeInsets.only(left: 10),
                      child: Text(shop !=null ?shop.toString():" ",
                        overflow: TextOverflow.fade,
                        maxLines: 2,
                        softWrap: true,
                        style: TextStyle(
                            color: Color(0xffffffff),
                            fontFamily: 'Montserrat',
                            fontSize: 18.0
                        ),
                      ),
                    ),
                    Container(
                      margin: EdgeInsets.only(left: 10,top: 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: <Widget>[
                          Icon(
                            Icons.alarm,
                            size: 17.0,
                            color: Color(0xffffffff),
                          ),
                          Text(
                            opening !=null ?'Opening Time - '+opening.toString():" ",
                            style:  TextStyle(
                                color: Color(0xffffffff),
                                fontFamily: 'Varela',
                                fontSize: 14.0),
                            softWrap: false,
                            overflow: TextOverflow.fade,
                          ),
                        ],
                      ),
                    ),
                    Container(
                      margin: EdgeInsets.only(left: 10,top: 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: <Widget>[
                          Icon(
                            Icons.alarm,
                            size: 17.0,
                            color: Color(0xffffffff),
                          ),
                          Text(
                            closing !=null ?'Closing Time - '+closing.toString():" ",
                            style:  TextStyle(
                                color: Color(0xffffffff),
                                fontFamily: 'Varela',
                                fontSize: 14.0),
                            softWrap: false,
                            overflow: TextOverflow.fade,
                          ),
                        ],
                      ),
                    ),
                    Container(
                      margin: EdgeInsets.only(left: 10,top: 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: <Widget>[
                          Icon(
                            Icons.rowing,
                            size: 17.0,
                            color: Color(0xffffffff),
                          ),
                          Text(
                            charge !=null ?'Delevery Charge - '+charge.toString():" ",
                            style:  TextStyle(
                                color: Color(0xffffffff),
                                fontFamily: 'Varela',
                                fontSize: 14.0),
                            softWrap: false,
                            overflow: TextOverflow.fade,
                          ),
                        ],
                      ),
                    ),
                    Container(
                      margin: EdgeInsets.only(left: 10,top: 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: <Widget>[
                          Icon(
                            Icons.location_on,
                            size: 17.0,
                            color: Color(0xffffffff),
                          ),
                          Text(
                            address !=null ?address.toString():" ",
                            style:  TextStyle(
                                color: Color(0xffffffff),
                                fontFamily: 'Varela',
                                fontSize: 12.0),
                            softWrap: false,
                            overflow: TextOverflow.fade,
                          ),
                        ],
                      ),
                    )
                  ]
              ),
              decoration: BoxDecoration(
                image: new DecorationImage(
                  image: new ExactAssetImage('assets/images/profile1.png'),
                  fit: BoxFit.cover,
                ),
              )
          ),
          new ListTile(
            leading: Icon(Icons.home),
            title: new Text("Home"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:0),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.shopping_basket),
            title: new Text("My Order"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:1,title: 'Order',),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.shopping_cart),
            title: new Text("My Shop"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => ShopDetailsWidget(),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.receipt),
            title: new Text("Product"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:2,title: 'My Product',),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.fastfood),
            title: new Text("Product Request"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => ProductRequestList(),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.library_books),
            title: new Text("Sales Report"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => SalesReport(),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.playlist_play),
            title: new Text("Transaction"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => Transaction(),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.contact_mail),
            title: new Text("Profile"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:3,title: 'Profile',),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.exit_to_app),
            title: new Text("Logout"),
            onTap: (){
              print( Provider.of<AuthProvider>(context,listen: false).logOut());
            },
          ),
        ],
      ),
    );
  }
}