/**
* Copyright (c) 2012 Jhonatan Salguero (www.novatoz.com)
*/
;(function() {
"use strict";
var ctx = Util.getContext(document.createElement("canvas")),
    abs = Math.abs;

/* check if a piece is in the right place */
function check_position(f1, f2) {
    // test rapido
    if (f1.rotation%360 || f2.rotation%360 || f2.hide || f1.hide || 
        (f1.row != f2.row && f1.col != f2.col)) { return; }

    var diff_x = f1.tx - f2.tx,
        diff_y = f1.ty - f2.ty,
        diff_col = f1.col - f2.col,
        diff_row = f1.row - f2.row,
        s = f1.size;

    if (((diff_col == -1 && diff_x < 0 && abs(diff_x+s) < 10) || 
        (diff_col == 1 && diff_x >= 0 && abs(diff_x-s) < 10))
        && (diff_y <= 10 && diff_y >= -10)) {
        return [f1.col > f2.col ? -abs(diff_x)+s : abs(diff_x)-s, f2.ty-f1.ty];

    } else if (((diff_row == -1 && diff_y < 0 && abs(diff_y+s) < 10) ||
                (diff_row == 1 && diff_y >= 0 && abs(diff_y-s) < 10))
                 && (diff_x <= 10 && diff_x >= -10)) {
        return [f2.tx-f1.tx, f1.row > f2.row ? -abs(diff_y)+s : abs(diff_y)-s];
    }
}

var Piece = Cevent.Shape.extend({
    type: "piece",

    /* edges is an array like ["inside", "outside", null, "inside"]*/
    init: function(x, y, img, size, edges) {
        this._super(x, y);
        var half_s = size / 2;
        this.img = img;
        this.size = size;
        this.edges = edges;
        
        // Change origin to center
        this.tx = this.x + half_s;
        this.ty = this.y + half_s;
        this.x = -half_s;
        this.y = -half_s;
    },
    
    /* draw piece path */
    draw_path: function(ctx) {
        var s = this.size, fn, i = 0;
        
        ctx.beginPath();
        ctx.moveTo(this.x, this.y);

        for ( ; i < 4; i++) {
            fn = this.edges[i];
            
            /* shaped side */
            if (fn) {
               // ctx.lineTo(this.x+.4*s, this.y);
                var cx = this[fn](ctx, s, this.x, this.y);
                //ctx.lineTo(cx+.4*s, this.y);
            
            /* flat side */
            } else {
                ctx.lineTo(this.x + s, this.y);
            }
            ctx.rotate(Math.PI / 2);
        }
        ctx.closePath();
    },
    
    /*
    render: function(ox, oy) {
        this.cache = {};
        for (var i = 0; i < 370; i+=45) {
            this.rotation = i;
            this.cache[i] = this._render(ox, oy);
        }
        this.rotation = 0;
        this.tx += this.offset;
    },
    //*/
    
    /* pre draw piece */
    render: function(ox, oy) {
        var ctx = Util.getContext(document.createElement("canvas")),
            s = this.size + .5;

        ctx.canvas.width = s * 2;
        ctx.canvas.height = s * 2;

        ctx.save();

        this.applyStyle(ctx);
        ctx.lineWidth = .5;
        ctx.translate(s, s);

        this.draw_path(ctx);
        ctx.clip();
        ctx.drawImage(this.img, -this.tx-ox, -this.ty-oy);
        if (this.stroke) {
            /* pseudo 3d
            ctx.shadowOffsetY = Math.cos(this.rotation*Math.PI/180);
            ctx.shadowOffsetX = Math.sin(this.rotation*Math.PI/180);
            //*/
            ctx.strokeStyle="#000";
            ctx.stroke();
        }
        
        ctx.restore();
        
        this.tx += this.offset;
        this.img = ctx.canvas;
    },
    
    outside: function(ctx, s, cx, cy) {
        ctx.lineTo(cx+s*.34, cy);
        
        ctx.bezierCurveTo(cx+s*.5, cy, cx+s*.4, cy+s*-0.15, cx+s*.4, cy+s*-0.15);
        
        
        ctx.bezierCurveTo(cx+s*.3, cy+s*-0.3, cx+s*.5, cy+s*-0.3, cx+s*.5, cy+s*-0.3);
        
        ctx.bezierCurveTo(cx+s*.7, cy+s*-0.3, cx+s*.6, cy+s*-0.15, cx+s*.6, cy+s*-0.15);
        
        ctx.bezierCurveTo(cx+s*.5, cy, cx+s*.65, cy, cx+s*.65, cy);
        
        ctx.lineTo(cx+s, cy);
    },
    
    
    inside: function(ctx, s, cx, cy){
        ctx.lineTo(cx+s*.35, cy);

        ctx.bezierCurveTo(cx+s*.505, cy+.05, cx+s*.405, cy+s*.155, cx+s*.405, cy+s*.1505);
        
        
        ctx.bezierCurveTo(cx+s*.3,  cy+s*.3, cx+s*.5,  cy+s*.3, cx+s*.5,  cy+s*.3);
        
        
        ctx.bezierCurveTo(cx+s*.7, cy+s*.29, cx+s*.6, cy+s*.15, cx+s*.6, cy+s*.15);
        
        ctx.bezierCurveTo(cx+s*.5, cy, cx+s*.65, cy, cx+s*.65, cy);
        
        ctx.lineTo(cx+s, cy);
        
    },

    /**/
    draw: function(ctx) {
        if (this.hide) { return; }

        var half_size = this.size / 2 - .5;

        this.setTransform(ctx);

        ctx.drawImage(this.img, this.x - half_size, this.y - half_size);
    },
    
    /* check position */
    check: function(other) {
        var r;
        if (other.type == "piece") {
            r = check_position(this, other);
        } else {
            var i, l = other.pieces.length;
            for (i = 0; i < l; i++) {
                if (r = check_position(this, other.pieces[i])) { break; };
            }
        }
        if (r) { this.rmove(r[0], r[1]); }
        return r;
    },
    
    /* is mouse over this piece? */
    hitTest: function(point) {
        if (this.hide) { return; }
        var s = this.size;
        
        ctx.save();
        this.setTransform(ctx);
        
        this.draw_path(ctx);
        
        ctx.restore();
        return ctx.isPointInPath(point.x, point.y);
    }
}),

Group = Cevent.Shape.extend({
    type: "group",
    
    init: function() {
        this.pieces = [];
        this._super(0, 0);
    },
    
    draw: function(ctx) {
        if (this.hide) { return; }

        var i, l = this.pieces.length;
        for (i = 0; i < l; i++) {
            this.pieces[i].draw(ctx);
        }
    },
    
    /* check every piece in this group */
    hitTest: function(point) {
        var i, l = this.pieces.length;
        for (i = 0; i < l; i++) {
            if (this.pieces[i].hitTest(point)){
                return true;
            }
        }
    },
    
    check: function(other) {
        var i, l = this.pieces.length, r;
        if (other.type == "piece") {
            for (i = 0; i < l; i++) {
                if (r = check_position(this.pieces[i], other)) { 
                    this.rmove(r[0], r[1]);
                    return true;
                }
            }
        } else {
            var j, l2 = other.pieces.length;
            for (i = 0; i < l; i++) {
                for (j = 0; j < l2; j++) {
                    if (r = check_position(this.pieces[i], other.pieces[j])) {
                        this.rmove(r[0], r[1]);
                        return true;
                    }
                } 
            }
        }

    },
    
    /* move each piece */
    rmove: function(x, y) {
        var i, l = this.pieces.length;
        for (i = 0; i < l; i++) { this.pieces[i].rmove(x, y); }
    },
    
    /* add new piece or merge with another group */
    add: function() {
        this.pieces = this.pieces.concat.apply(this.pieces, arguments);
    }
});

/* Register objects in canvas-event framework */
Cevent.register("group", Group);
Cevent.register("piece", Piece);
}())
