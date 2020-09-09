import 'dart:async';

import 'package:flutter/material.dart';

class CircularLoadingWidget extends StatefulWidget {
  double height;
  String subtitleText;

  CircularLoadingWidget({Key key, this.height,this.subtitleText }) : super(key: key);

  @override
  _CircularLoadingWidgetState createState() => _CircularLoadingWidgetState();
}

class _CircularLoadingWidgetState extends State<CircularLoadingWidget> with SingleTickerProviderStateMixin {
  Animation<double> animation;
  AnimationController animationController;
  bool  loding = false;
  void initState() {
    super.initState();
    animationController = AnimationController(duration: Duration(milliseconds: 300), vsync: this);
    CurvedAnimation curve = CurvedAnimation(parent: animationController, curve: Curves.easeOut);
    animation = Tween<double>(begin: widget.height, end: 0).animate(curve)
      ..addListener(() {
        if (mounted) {
          setState(() {
          });
        }
      });
    Timer(Duration(seconds: 7), () {
      if (mounted) {
        setState(() {
          loding =true;
        });
      }

    });
  }

  @override
  void dispose() {
    animationController.dispose();
    loding =false;
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      child: SizedBox(
        height: animation.value,
        child: new Center(
          child: loding == false ?
            new CircularProgressIndicator() :Container(child:new Text(widget.subtitleText)),
        ),
      ),
    );
  }
}
