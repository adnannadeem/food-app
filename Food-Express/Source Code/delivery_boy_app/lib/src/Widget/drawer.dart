
import 'package:flutter/material.dart';
import 'package:deliveryboyapp/main.dart';
import 'package:provider/provider.dart';
import 'package:deliveryboyapp/providers/auth.dart';

class AppDrawer extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final userName =   Provider.of<AuthProvider>(context,listen: false).userName;
    final userEmail =   Provider.of<AuthProvider>(context,listen: false).userEmail;
    final userImg =   Provider.of<AuthProvider>(context,listen: false).userImg;

    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: <Widget>[
          new UserAccountsDrawerHeader(
            accountName: new Text(userName !=null ?userName.toString():" ",style: TextStyle(color: Color(0xffffffff))),
            accountEmail: new Text(userEmail !=null ?userEmail.toString():" ",style: TextStyle(color: Color(0xffffffff))),
            decoration: new BoxDecoration(
              image: new DecorationImage(
                image: new ExactAssetImage('assets/images/profile1.png'),
                fit: BoxFit.cover,
              ),
            ),
            currentAccountPicture: CircleAvatar(
                backgroundImage: userImg !=null ? NetworkImage(userImg) :ExactAssetImage('assets/images/profile.png')),
          ),
          new ListTile(
            leading: Icon(Icons.fastfood),
            title: new Text("Orders"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:0),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.shopping_basket),
            title: new Text("Orders History"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:1,title: 'Orders History',),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.contact_mail),
            title: new Text("Profile"),
            onTap: () {
              Navigator.of(context).push(MaterialPageRoute(
                builder: (context) => MyHomePage(tabsIndex:2,title: 'Profile',),
              )
              );
            },
          ),
          new ListTile(
            leading: Icon(Icons.exit_to_app),
            title: new Text("Logout"),
            onTap: (){
              Provider.of<AuthProvider>(context,listen: false).logOut();
            },
          ),
        ],
      ),
    );
  }

  Widget _createHeader() {
    return DrawerHeader(
        margin: EdgeInsets.zero,
        padding: EdgeInsets.zero,
        decoration: BoxDecoration(
            image: DecorationImage(
                fit: BoxFit.fill,
                image: AssetImage('res/images/drawer_header_background.png'))),
        child: Stack(children: <Widget>[
          Positioned(
              bottom: 12.0,
              left: 16.0,
              child: Text("Flutter Step-by-Step",
                  style: TextStyle(
                      color: Colors.white,
                      fontSize: 20.0,
                      fontWeight: FontWeight.w500))),
        ]));
  }
}
