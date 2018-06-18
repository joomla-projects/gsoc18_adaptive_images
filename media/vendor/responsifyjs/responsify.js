(function ( $ ) {
  $.fn.responsify = function() {
    return this.each(function() {
      var owidth, oheight,
          mwidth, mheight,
          twidth, theight,
          fx1, fy1, fx2, fy2,
          width, height, top, left,
          $this = $(this);
      mwidth = $this[0].naturalWidth;
      mheight = $this[0].naturalHeight;
      owidth = $this.width();
      oheight = $this.height();
      twidth = $this.parent().width();
      theight = $this.parent().height();
      fx1 = Number($this.attr('data-focus-left'));
      fy1 = Number($this.attr('data-focus-top'));
      fx2 = Number($this.attr('data-focus-right'));
      fy2 = Number($this.attr('data-focus-bottom'));
      if( owidth/oheight >= twidth/theight ) {
        var fwidth = (fx2-fx1) * owidth;
        if ( fwidth/oheight >= twidth/theight ) {
          height = oheight*twidth/fwidth;
          width = owidth*twidth/fwidth;
          left = -fx1*width;
          top = (theight-height)/2;
        } else {
          height = theight;
          width = theight*owidth/oheight;
          left = twidth/2 - (fx1 + fx2)*width/2;
          left = left>0?0:left;
          top = 0;
        }
      }
      else {
        var fheight = (fy2-fy1) * oheight;
        if ( fheight/owidth >= theight/twidth ) {
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
      $this.parent().css({
        "overflow": "hidden",
        "max-height":mheight,
        "max-width":mwidth
      })
      $this.css({
        "position": "relative",
        "max-height":mheight,
        "max-width":mwidth,
        "height": height,
        "width": width,
        "left": left,
        "top": top
      })
    });
  };
}( jQuery ));
