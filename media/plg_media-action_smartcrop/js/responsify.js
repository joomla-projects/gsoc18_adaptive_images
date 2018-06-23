function responsify(item){
  var owidth, oheight,
      twidth, theight,
      mwidth, mheight,
      fx1, fy1, fx2, fy2,
      width, height, top, left;
  
  mwidth = item.naturalWidth;
  mheight = item.naturalHeight;
  owidth = item.offsetWidth;
  oheight = item.offsetHeight;
  twidth = item.parentNode.clientWidth;
  theight = item.parentNode.clientHeight;

  fx1 = Number(item.attributes['data-focus-left'].value);
  fy1 = Number(item.attributes['data-focus-top'].value);
  fx2 = Number(item.attributes['data-focus-right'].value);
  fy2 = Number(item.attributes['data-focus-bottom'].value);
  if( owidth/oheight > twidth/theight ) {
    var fwidth = (fx2-fx1) * owidth;
    if ( fwidth/oheight > twidth/theight ) {
      height = oheight*twidth/fwidth;
      width = owidth*twidth/fwidth;
      left = -fx1*width;
      top = (theight-height)/2;
    } else {
      height = theight;
      width = theight*owidth/oheight;
      left = twidth/2 - (fx1 + fx2)*width/2;
      top = 0;
    }
  }
  else {
    var fheight = (fy2-fy1) * oheight;
    if ( fheight/owidth > theight/twidth ) {
      width = owidth*theight/fheight;
      height = oheight*theight/fheight;
      top = -fy1*height;
      left = (twidth-width)/2;
    } else {
      width = twidth;
      height = twidth*oheight/owidth;
      top = theight/2 - (fy1 + fy2)*height/2;
      left = 0;
    }
  }

  var parentCSS = " overflow:hidden;" + 
                  " max-width:" + mwidth + ";" +
                  " width:" + document.body.clientWidth + ";" ;
  item.parentNode.setAttribute("style", parentCSS)

  var newCSS = " position:relative;"+ 
               " height:" + height + "px;"+
               " width:" + width + "px;" +
               " top:" + top + "px;" +
               " left:" + left + "px;" +
               " max-height:" + mheight + ";" +
               " max-width:" + mwidth + ";" ;
  item.setAttribute("style", newCSS);
}

window.onload = function() {
items = document.getElementsByClassName("adaptiveimg");
for( $index = 0 ; $index < items.length ; $index++){
  items[$index].removeAttribute("style");
  responsify(items[$index]);
}
}

window.onresize = function() {
items = document.getElementsByClassName("adaptiveimg");
for( $index = 0 ; $index < items.length ; $index++){
  items[$index].removeAttribute("style");
  responsify(items[$index]);
}
}