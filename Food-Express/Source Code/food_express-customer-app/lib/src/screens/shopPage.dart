import 'dart:io';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:foodexpress/config/api.dart';
import 'package:foodexpress/providers/auth.dart';
import 'package:foodexpress/src/Widget/CircularLoadingWidget.dart';
import 'package:foodexpress/src/screens/Category.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:geolocator/geolocator.dart';


class ShopPage extends StatefulWidget {
  @override
  _ShopPageState createState() {
    return new _ShopPageState();
  }
}


class _ShopPageState extends State<ShopPage> {
  TextEditingController editingController = TextEditingController();
  GlobalKey<RefreshIndicatorState> refreshKey;
  Position _currentPosition;
  Geolocator _geolocator = Geolocator();
  final Geolocator geolocator = Geolocator()..forceAndroidLocationManager;
  void checkPermission() {
    _geolocator.checkGeolocationPermissionStatus().then((status) { print('status: $status'); });
    _geolocator.checkGeolocationPermissionStatus(locationPermission: GeolocationPermission.locationAlways).then((status) { print('always status: $status'); });
    _geolocator.checkGeolocationPermissionStatus(locationPermission: GeolocationPermission.locationWhenInUse)..then((status) { print('whenInUse status: $status'); });
  }


  String api = FoodApi.baseApi;
  String _selectedLocation = '1';
    String _selectedArea ='1';
    List _locations = List();
    List _areas = List();
    List _shops = List();

    Future<void> setting() async {
      await Provider.of<AuthProvider>(context,listen: false).setting();
  }
    Future<String> getLocations(latitude,longitude) async {
      final url = "$api/locations";
      var response = await http.get(url, headers: {"X-FOOD-LAT":"$latitude","X-FOOD-LONG":"$longitude","Accept": "application/json"});
      var resBody = json.decode(response.body);
      if (response.statusCode == 200) {
        setState(() {
          _locations = resBody['data'];
        });
      } else {
        throw Exception('Failed to data');
      }
      return "Sucess";
    }
    Future<String> getArea(String locationID,latitude,longitude) async {
      final url = "$api/locations/$locationID/areas";
      var response = await http.get(url, headers: {"X-FOOD-LAT":"$latitude","X-FOOD-LONG":"$longitude","Accept": "application/json"});
      var resBody = json.decode(response.body);
      if (response.statusCode == 200) {
        setState(() {
          _areas = resBody['data'];
        });
      } else {
        throw Exception('Failed to');
      }

      return "Sucess";
    }
    Future<String> getShops(String areaID,latitude,longitude) async {
      final url = areaID !=null?"$api/areas?id=$areaID":'$api/areas';
      var response = await http.get(url, headers: {"X-FOOD-LAT":"$latitude","X-FOOD-LONG":"$longitude","Accept": "application/json"});
      var resBody = json.decode(response.body);
      print(resBody);
      if (response.statusCode == 200) {
        setState(() {
          _shops.clear();
          _shops = resBody['data'];
        });
      } else {
        throw Exception('Failed to data');
      }
      return "Sucess";
    }
    void SerchShop(value, latitude,longitude) async {
      final url = "$api/search/$value/shops";
      var response = await http.get(url, headers: {"X-FOOD-LAT":"$latitude","X-FOOD-LONG":"$longitude","Accept": "application/json"});
      var resBody = json.decode(response.body);
      if (response.statusCode == 200) {
        setState(() {
          _shops.clear();
          _shops = resBody['data'];
        });

      } else {
        throw Exception('Failed to data');
      }
      return;
    }

  Future<Null> refreshList(area) async {
    setState(() {
      _shops.clear();
      this.setting();
      this.getShops(area,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      this.getLocations(_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      _getCurrentLocation();

    });
  }
  initAuthProvider(context) async {
    Provider.of<AuthProvider>(context,listen: false).initAuthProvider();
  }
    @override
    void initState() {
      super.initState();
      _getCurrentLocation();
      checkPermission();
      this.setting();
      initAuthProvider(context);
    }
  _getCurrentLocation() {
    geolocator
        .getCurrentPosition(desiredAccuracy: LocationAccuracy.best)
        .then((Position position) {
      setState(() {
        _selectedArea=null;
        _selectedLocation=null;
        _currentPosition = position;
        this.getLocations(_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
        this.getArea(_selectedLocation,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
        this.getShops(_selectedArea,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      });
    }).catchError((e) {
      print(e);
      this.getLocations(_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      this.getArea(_selectedLocation,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      this.getShops(_selectedArea,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
      checkPermission();
    });
  }
   @override
  Widget build(BuildContext context) {
     return Scaffold(
        backgroundColor: Color(000),
        body: SafeArea(
          child:
        RefreshIndicator(
        key: refreshKey,
     onRefresh: () async {
     await refreshList(_selectedArea);
     },
          child: new LayoutBuilder(
          builder: (BuildContext context, BoxConstraints constraints) {
        return ListView(
          children: <Widget>[
            SizedBox(height: 10.0),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: <Widget>[
            Padding(
            padding: EdgeInsets.only(top: 10.0, left: 10.0, right: 10.0),
                child:
                Container(
                  height: MediaQuery.of(context).size.height /13.5,
                  width: MediaQuery.of(context).size.width/2.2,
                  padding: EdgeInsets.all(2.0),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.only(
                        bottomRight: Radius.circular(10.0),
                        bottomLeft: Radius.circular(10.0),
                        topLeft: Radius.circular(10.0),
                        topRight: Radius.circular(10.0),
                      )),
                  child: Row(
                    children: <Widget>[
                      SvgPicture.asset("assets/icons/maps-and-flags.svg"),
                      SizedBox(width: 10),
                      Expanded(
                        child:
                        DropdownButton(
                          isExpanded: true,
                          underline: SizedBox(width: 20,),
                          icon: SvgPicture.asset("assets/icons/dropdown.svg"),
                          hint: Text('choose a location',overflow: TextOverflow.fade,
                            maxLines: 1,
                            softWrap: false,), // Not necessary for Option 1
                            value: _selectedLocation != null ? _selectedLocation:null,
                            onChanged: (location) {
                            setState(() {
                              _selectedLocation = location;
                              _shops.clear();
                              _areas.clear();
                              _selectedArea = null;
                              this.getArea(location,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
                            });
                          },
                          items:  _locations.length>0 ? _locations.map((location) {
                            return DropdownMenuItem(
                              child: new Text(location['name'] !=null ?location['name']: '',overflow: TextOverflow.fade,
                                maxLines: 1,
                                softWrap: false,),
                              value: location['id'].toString(),
                            );
                          }).toList():null,
                        ),
                      ),
                    ],
                  ),
                ),
            ),
            Padding(
            padding: EdgeInsets.only(top: 10.0, left: 10.0, right: 10.0),
            child:
                Container(
                  height: MediaQuery.of(context).size.height /13.5,
                  width: MediaQuery.of(context).size.width/2.3,
                  padding: EdgeInsets.all(3.0),

                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.only(
                        bottomRight: Radius.circular(10.0),
                        bottomLeft: Radius.circular(10.0),
                        topLeft: Radius.circular(10.0),
                        topRight: Radius.circular(10.0),
                      )),
                  child: Row(
                    children: <Widget>[
                      SvgPicture.asset("assets/icons/maps-and-flags.svg"),
                      SizedBox(width: 10),
                      Expanded(
                        child:
                        DropdownButton(
                          isExpanded: true,
                          underline: SizedBox(width: 20,),
                          icon: SvgPicture.asset("assets/icons/dropdown.svg"),
                          hint: Text('choose a Area',overflow: TextOverflow.fade,
                            maxLines: 1,
                            softWrap: false,), // Not necessary for Option 1
                          value: _selectedArea != null ? _selectedArea:null,
                          onChanged: ( area) {
                            setState(() {
                              _selectedArea = area;
                              _shops.clear();
                              getShops(area,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
                            });
                          },
                          items: _areas.map((area) {
                            return DropdownMenuItem(
                              child: new Text(area['name'],overflow: TextOverflow.fade,
                                maxLines: 1,
                                softWrap: false,),
                              value: area['id'].toString(),
                            );
                          }).toList(),
                        ),
                      ),
                    ],
                  ),
                ),
                    )
              ],
            ),
            Padding(
              padding: EdgeInsets.only(top: 15.0, left: 15.0, right: 15.0),
              child:
              Container(
                decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.only(
                      bottomRight: Radius.circular(10.0),
                      bottomLeft: Radius.circular(10.0),
                      topLeft: Radius.circular(10.0),
                      topRight: Radius.circular(10.0),
                    )),
                child: TextField(
                  textInputAction: TextInputAction.search,
                  onSubmitted: (value){
                      SerchShop(value != null? value:null,_currentPosition != null?_currentPosition.latitude:'',_currentPosition != null?_currentPosition.longitude:'');
                },
                  controller: editingController,
                  decoration: InputDecoration(
                    border: InputBorder.none,
                    contentPadding: EdgeInsets.only(top: 14.0),
                    hintText: 'Search for shops',
                    hintStyle:
                    TextStyle(fontFamily: 'Montserrat', fontSize: 14.0),
                    prefixIcon: Icon(Icons.search, color: Colors.grey),
                  ),
                ),
              ),
            ),

            SizedBox(height: 15.0),
          _shops.isEmpty ?
                    CircularLoadingWidget(height: 200,subtitleText: 'No shops found ',) :
            Container(
                height: MediaQuery.of(context).size.height /1.5,
                width: MediaQuery.of(context).size.width/2,
                padding: EdgeInsets.only(left:10.0,right: 10.0),
                child: new GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  childAspectRatio: MediaQuery.of(context).size.width /
                      (MediaQuery.of(context).size.height / 1.6),
                  primary: false,
                  children: _shops.map((shop) {
                    return _buildCard(shop['name'],shop['image'],shop['address'],shop['id']);
                   }).toList(),

                )
            ),
          ],
        );
     })
        )
        )
    );
  }



    Widget _buildCard(String name, String imgPath, String address, int shopID) {
      return InkWell(
        highlightColor: Colors.transparent,
        splashColor: Colors.white,
        onTap: () {
          Navigator.of(context).push(MaterialPageRoute(
            builder: (context) => Category(shopID:'$shopID',shopName: name),
          )
          );
          },
        child: Container(
          margin: EdgeInsets.all(4),
          padding: EdgeInsets.all(14),
          decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.all(Radius.circular(5)),
              boxShadow: [
                BoxShadow(color: Theme.of(context).focusColor.withOpacity(0.05), offset: Offset(0, 5), blurRadius: 5)
              ]),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Container(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(5.0),
                  child: Image.network(
                    imgPath,
                    fit: BoxFit.cover,
                    height: 100,
                    width: double.infinity,

                  ),

                ),
              ),
              SizedBox(height: 7),
              Text(
                name != null ? name : '',
                style:  TextStyle(
                    color: Color(0xFF575E67),
                    fontFamily: 'Varela',
                    fontSize: 15.0),
                softWrap: false,
                maxLines: 1,
                overflow: TextOverflow.fade,
              ),
              SizedBox(height: 4),

              Row(
                mainAxisSize: MainAxisSize.max,
                children: <Widget>[
                  Icon(
                    Icons.location_on,
                    size: 16.0,
                    color: Colors.amber.shade500,
                  ),
                  Text(
                    address != null ? address : '',
                    style:  TextStyle(
                    color: Color(0xFF575E67),
                    fontFamily: 'Varela',
                    fontSize: 11.0),
                    softWrap: false,
                    maxLines: 1,
                    overflow: TextOverflow.fade,
                  ),
                ],
              ),
              SizedBox(height: 18),

            ],
          ),
        ),
      );

    }
}
