/**
 * Yandex.Maps Clusterer v1.0
 * http://vremenno.net
 *
 * Copyright 2010, Vremenno.net & Eugene Kotelnikov
 */


/**
 * @name PlacemarkClustererOptions
 * @class This class represents optional arguments to the {@link PlacemarkClusterer}
 * constructor.
 * @property {Number} [maxZoom=8] The max zoom level monitored by a
 * placemark cluster. If not given, the placemark cluster assumes the maximum map
 * zoom level. When maxZoom is reached or exceeded all placemarks will be shown
 * without cluster.
 * @property {Number} [gridSize=60] The grid size of a cluster in pixel. Each
 * cluster will be a square. If you want the algorithm to run faster, you can set
 * this value larger.
 * @property {PlacemarkStyleOptions} [style]
 * Custom style for the cluster placemarks.
 * The array should be ordered according to increasing cluster size,
 * with the style for the smallest clusters first, and the style for the
 * largest clusters last.
 */

/**
 * @name PlacemarkStyleOptions
 * @class An array of these is passed into the {@link PlacemarkClustererOptions}
 * style option.
 * @property {String} [url] Image url.
 * @property {Number} [height] Image height.
 * @property {Number} [height] Image width.
 * @property {Array of Number} [opt_anchor] Anchor for label text, like [24, 12]. 
 *    If not set, the text will align center and middle.
 * @property {String} [opt_textColor="black"] Text color.
 */

/**
 * Creates a new PlacemarkClusterer to cluster placemarks on the map.
 *
 * @constructor
 * @param {YMap} map The map that the placemarks should be added to.
 * @param {Array of YMaps.Placemark} opt_placemarks Initial set of placemarks to be clustered.
 * @param {PlacemarkClustererOptions} opt_opts A container for optional arguments.
 */
 
 
function PlacemarkClusterer(map, opt_placemarks, opt_opts) {
  // private members
  var clusters_ = [];
  var map_ = map;
  var maxZoom_ = null;
  var me_ = this;
  var gridSize_ = 60;
  var leftPlacemarks_ = [];
  var mcfn_ = null;

  var style_ = {
    'url': "http://gmaps-utility-library.googlecode.com/svn/trunk/markerclusterer/images/m1.png",
    'height': 53,
    'width': 53
  };

  if (typeof opt_opts === "object" && opt_opts !== null) {
    if (typeof opt_opts.gridSize === "number" && opt_opts.gridSize > 0) {
      gridSize_ = opt_opts.gridSize;
    }
    if (typeof opt_opts.maxZoom === "number") {
      maxZoom_ = opt_opts.maxZoom;
    }
    if (typeof opt_opts.style === "object" && opt_opts.style !== null && opt_opts.style.length !== 0) {
      style_ = opt_opts.style;
    }
  }

  /**
   * When we add a placemark, the placemark may not in the viewport of map, then we don't deal with it, instead
   * we add the placemark into a array called leftPlacemarks_. When we reset PlacemarkClusterer we should add the
   * leftPlacemarks_ into PlacemarkClusterer.
   */
  function addLeftPlacemarks_() {
    if (leftPlacemarks_.length === 0) {
      return;
    }
    var leftPlacemarks = [];
    for (i = 0; i < leftPlacemarks_.length; ++i) {
      me_.addPlacemark_(leftPlacemarks_[i], true, null, null, true);
    }
    leftPlacemarks_ = leftPlacemarks;
  }

  /**
   * Get cluster placemark images of this placemark cluster. Mostly used by {@link Cluster}
   * @private
   * @return {Object}
   */
  this.getStyle_ = function () {
    return style_;
  };

  /**
   * Remove all placemarks from PlacemarkClusterer.
   */
  this.clearPlacemarks = function () {
    for (var i = 0; i < clusters_.length; ++i) {
      if (typeof clusters_[i] !== "undefined" && clusters_[i] !== null) {
        clusters_[i].clearPlacemarks();
      }
    }
    clusters_ = [];
    leftPlacemarks_ = [];
    mcfn_.disable();
  };

  /**
   * Check a placemark, whether it is in current map viewport.
   * @private
   * @return {Boolean} if it is in current map viewport
   */
  function isPlacemarkInViewport_(placemark) {
    return map_.getBounds().contains(placemark.getCoordPoint());
  }

  /**
   * When reset PlacemarkClusterer, there will be some placemarks get out of its cluster.
   * These placemarks should be add to new clusters.
   * @param {Array of YMaps.Placemark} placemarks Placemarks to add.
   */
  function reAddPlacemarks_(placemarks) {
    var len = placemarks.length;
    var clusters = [];
    for (var i = len - 1; i >= 0; --i) {
      me_.addPlacemark_(placemarks[i].placemark, true, placemarks[i].isAdded, clusters, true);
    }
    addLeftPlacemarks_();
  }

  /**
   * Add a placemark.
   * @private
   * @param {YMaps.Placemark} placemark Placemark you want to add
   * @param {Boolean} opt_isNodraw Whether redraw the cluster contained the placemark
   * @param {Boolean} opt_isAdded Whether the placemark is added to map. Never use it.
   * @param {Array of Cluster} opt_clusters Provide a list of clusters, the placemark
   *     cluster will only check these cluster where the placemark should join.
   */
  this.addPlacemark_ = function (placemark, opt_isNodraw, opt_isAdded, opt_clusters, opt_isNoCheck) {
    if (opt_isNoCheck !== true) {
      if (!isPlacemarkInViewport_(placemark)) {
        leftPlacemarks_.push(placemark);
        return;
      }
    }

	var isAdded  = opt_isAdded;
	var isNodraw = (opt_isNodraw === true);
	var clusters = opt_clusters;
    var pos = map_.converter.coordinatesToMapPixels(placemark.getCoordPoint());
    
    if (typeof isAdded !== "boolean") {
      isAdded = false;
    }
    if (typeof clusters !== "object" || clusters === null) {
      clusters = clusters_;
    }

    var length = clusters.length;
    var cluster = null;
    for (var i = length - 1; i >= 0; i--) {
      cluster = clusters[i];
      var center = cluster.getCenter();
      if (center === null) {
        continue;
      }

      center = map_.converter.coordinatesToMapPixels(center);

      // Found a cluster which contains the placemark.
      if (pos.x >= center.x - gridSize_ && pos.x <= center.x + gridSize_ &&
          pos.y >= center.y - gridSize_ && pos.y <= center.y + gridSize_) {
        cluster.addPlacemark({
          'isAdded': isAdded,
          'placemark': placemark
        });
        if (!isNodraw) {
          cluster.redraw_();
        }
        return;
      }
    }

    // No cluster contain the placemark, create a new cluster.
    cluster = new Cluster(this, map);
    cluster.addPlacemark({
      'isAdded': isAdded,
      'placemark': placemark
    });
    if (!isNodraw) {
      cluster.redraw_();
    }

    // Add this cluster both in clusters provided and clusters_
    clusters.push(cluster);
    if (clusters !== clusters_) {
      clusters_.push(cluster);
    }
  };

  /**
   * Remove a placemark.
   *
   * @param {YMaps.Placemark} placemark The placemark you want to remove.
   */

  this.removePlacemark = function (placemark) {
    for (var i = 0; i < clusters_.length; ++i) {
      if (clusters_[i].remove(placemark)) {
        clusters_[i].redraw_();
        return;
      }
    }
  };

  /**
   * Redraw all clusters in viewport.
   */
  this.redraw_ = function () {
    var clusters = this.getClustersInViewport_();
    for (var i = 0; i < clusters.length; ++i) {
      clusters[i].redraw_(true);
    }
  };

  /**
   * Get all clusters in viewport.
   * @return {Array of Cluster}
   */
  this.getClustersInViewport_ = function () {
    var clusters = [];
    var curBounds = map_.getBounds();
    for (var i = 0; i < clusters_.length; i ++) {
      if (clusters_[i].isInBounds(curBounds)) {
        clusters.push(clusters_[i]);
      }
    }
    return clusters;
  };

  /**
   * Get max zoom level.
   * @private
   * @return {Number}
   */
  this.getMaxZoom_ = function () {
    return maxZoom_;
  };

  /**
   * Get map object.
   * @private
   * @return {YMaps.Map}
   */
  this.getMap_ = function () {
    return map_;
  };

  /**
   * Get grid size
   * @private
   * @return {Number}
   */
  this.getGridSize_ = function () {
    return gridSize_;
  };

  /**
   * Get total number of placemarks.
   * @return {Number}
   */
  this.getTotalPlacemarks = function () {
    var result = 0;
    for (var i = 0; i < clusters_.length; ++i) {
      result += clusters_[i].getTotalPlacemarks();
    }
    return result;
  };

  /**
   * Get total number of clusters.
   * @return {int}
   */
  this.getTotalClusters = function () {
    return clusters_.length;
  };

  /**
   * Collect all placemarks of clusters in viewport and regroup them.
   */
  this.resetViewport = function () {
    var clusters = this.getClustersInViewport_();
    //console.log(clusters);
    var tmpPlacemarks = [];

    for (var i = 0; i < clusters.length; ++i) {
      var cluster = clusters[i];
      var oldZoom = cluster.getCurrentZoom();
      if (oldZoom === null) {
        continue;
      }
      var curZoom = map_.getZoom();
      if (curZoom !== oldZoom) {
        // If the cluster zoom level changed then destroy the cluster
        // and collect its placemarks.
        var placemarks = cluster.getPlacemarks();
        for (var j = 0; j < placemarks.length; ++j) {
          var newPlacemark = {
            'isAdded': false,
            'placemark': placemarks[j].placemark
          };
          tmpPlacemarks.push(newPlacemark);
        }
        cluster.clearPlacemarks();
        for (j = 0; j < clusters_.length; ++j) {
          if (cluster === clusters_[j]) {
            clusters_.splice(j, 1);
          }
        }
      }
    }

    // Add the placemarks collected into placemark cluster to reset
    reAddPlacemarks_(tmpPlacemarks);
    this.redraw_();
  };


  /**
   * Add a set of placemarks.
   *
   * @param {Array of YMaps.Placemark} placemarks The placemarks you want to add.
   */
  this.addPlacemarks = function (placemarks) {
    for (var i = 0; i < placemarks.length; ++i) {
      this.addPlacemark_(placemarks[i], true, false);
    }
    this.redraw_();
  };
  
  this.addPlacemark = function(placemark) {
	this.addPlacemark_(placemark, false, false);
  };

  // initialize
  if (typeof opt_placemarks === "object" && opt_placemarks !== null) {
    this.addPlacemarks(opt_placemarks);
  }

  // when map move end, regroup.
  mcfn_ = YMaps.Events.observe(map, map.Events.Update, function () {
    me_.resetViewport();
  });
}

/**
 * Create a cluster to collect placemarks.
 * A cluster includes some placemarks which are in a block of area.
 * If there are more than one placemarks in cluster, the cluster
 * will create a {@link ClusterPlacemark_} and show the total number
 * of placemarks in cluster.
 *
 * @constructor
 * @private
 * @param {PlacemarkClusterer} placemarkClusterer The placemark cluster object
 */
function Cluster(placemarkClusterer) {
  var center_ = null;
  var placemarks_ = [];
  var placemarkClusterer_ = placemarkClusterer;
  var map_ = placemarkClusterer.getMap_();
  var clusterPlacemark_ = null;
  var zoom_ = map_.getZoom();

  /**
   * Get placemarks of this cluster.
   *
   * @return {Array of YMaps.Placemark}
   */
  this.getPlacemarks = function () {
    return placemarks_;
  };

  /**
   * If this cluster intersects certain bounds.
   *
   * @param {YMaps.Bounds} bounds A bounds to test
   * @return {Boolean} Is this cluster intersects the bounds
   */
  this.isInBounds = function (bounds) {
    if (center_ === null) {
      return false;
    }

    if (!bounds) {
      bounds = map_.getBounds();
    }
    
    var sw = map_.converter.coordinatesToMapPixels(bounds.getLeftBottom());
    var ne = map_.converter.coordinatesToMapPixels(bounds.getRightTop());

    var centerxy = map_.converter.coordinatesToMapPixels(center_);
    var inViewport = true;
    var gridSize = placemarkClusterer.getGridSize_();
    if (zoom_ !== map_.getZoom()) {
      var dl = map_.getZoom() - zoom_;
      gridSize = Math.pow(2, dl) * gridSize;
    }
    if (ne.x !== sw.x && (centerxy.x + gridSize < sw.x || centerxy.x - gridSize > ne.x)) {
      inViewport = false;
    }
    if (inViewport && (centerxy.y + gridSize < ne.y || centerxy.y - gridSize > sw.y)) {
      inViewport = false;
    }
    return inViewport;
  };

  /**
   * Get cluster center.
   *
   * @return {YMaps.GeoPoint}
   */
  this.getCenter = function () {
    return center_;
  };

  /**
   * Add a placemark.
   *
   * @param {Object} placemark An object of placemark you want to add:
   *   {Boolean} isAdded If the placemark is added on map.
   *   {YMaps.Placemark} placemark The placemark you want to add.
   */
  this.addPlacemark = function (placemark) {
    if (center_ === null) {
      /*var pos = placemark['placemark'].getLatLng();
       pos = map.fromLatLngToContainerPixel(pos);
       pos.x = parseInt(pos.x - pos.x % (GRIDWIDTH * 2) + GRIDWIDTH);
       pos.y = parseInt(pos.y - pos.y % (GRIDWIDTH * 2) + GRIDWIDTH);
       center = map.fromContainerPixelToLatLng(pos);*/
      center_ = placemark.placemark.getCoordPoint();
    }
    placemarks_.push(placemark);
  };

  /**
   * Remove a placemark from cluster.
   *
   * @param {YMaps.Placemark} placemark The placemark you want to remove.
   * @return {Boolean} Whether find the placemark to be removed.
   */
  this.removePlacemark = function (placemark) {
    for (var i = 0; i < placemarks_.length; ++i) {
      if (placemark === placemarks_[i].placemark) {
        if (placemarks_[i].isAdded) {
          map_.removeOverlay(placemarks_[i].placemark);
        }
        placemarks_.splice(i, 1);
        return true;
      }
    }
    return false;
  };

  /**
   * Get current zoom level of this cluster.
   * Note: the cluster zoom level and map zoom level not always the same.
   *
   * @return {Number}
   */
  this.getCurrentZoom = function () {
    return zoom_;
  };

  /**
   * Redraw a cluster.
   * @private
   * @param {Boolean} isForce If redraw by force, no matter if the cluster is
   *     in viewport.
   */
  this.redraw_ = function (isForce) {
    if (!isForce && !this.isInBounds()) {
      return;
    }
    
    // Set cluster zoom level.
    zoom_ = map_.getZoom();
    var i = 0;
    var mz = placemarkClusterer.getMaxZoom_();
    if (mz === null) {
      mz = map_.getMaxZoom();
    }
    if (zoom_ >= mz || this.getTotalPlacemarks() === 1) {
      // If current zoom level is beyond the max zoom level or the cluster
      // have only one placemark, the placemark(s) in cluster will be showed on map.
      for (i = 0; i < placemarks_.length; ++i) {
        if (placemarks_[i].isAdded) {
          if (placemarks_[i].placemark.isHidden()) {
            placemarks_[i].placemark.show();
          }
        } else {
          map_.addOverlay(placemarks_[i].placemark);
          placemarks_[i].isAdded = true;
        }
      }
      if (clusterPlacemark_ !== null) {
        clusterPlacemark_.hide();
      }
    } else {
      // Else add a cluster placemark on map to show the number of placemarks in
      // this cluster.
      for (i = 0; i < placemarks_.length; ++i) {
        if (placemarks_[i].isAdded && (!placemarks_[i].placemark.isHidden())) {
          placemarks_[i].placemark.hide();
        }
        if (!placemarks_[i].isAdded && placemarks_[i].placemark.isHidden()) {
          placemarks_[i].placemark.show();
        }
      }
      if (clusterPlacemark_ === null) {
        clusterPlacemark_ = new ClusterPlacemark_(center_, this.getTotalPlacemarks(), placemarkClusterer_.getStyle_(), placemarkClusterer_.getGridSize_());
        map_.addOverlay(clusterPlacemark_);
      } else {
		clusterPlacemark_.setCount(this.getTotalPlacemarks());
        if (clusterPlacemark_.isHidden()) {
          clusterPlacemark_.show();
        }
        clusterPlacemark_.onMapUpdate();
      }
    }
  };

  /**
   * Remove all the placemarks from this cluster.
   */
  this.clearPlacemarks = function () {
    if (clusterPlacemark_ !== null) {
      map_.removeOverlay(clusterPlacemark_);
    }
    for (var i = 0; i < placemarks_.length; ++i) {
      if (placemarks_[i].isAdded) {
        map_.removeOverlay(placemarks_[i].placemark);
      }
    }
    placemarks_ = [];
  };

  /**
   * Get number of placemarks.
   * @return {Number}
   */
   
  this.getTotalPlacemarks = function () {
    return placemarks_.length;
  };
}

function ClusterPlacemark_(point, count, style, padding) {
	this.count_ = count;
	
	this.url_ = style.url;
	this.width_ = style.width;
	this.height_ = style.height;
	this.textColor_ = style.textColor;
	
	this.padding_ = padding;
	
	var me_ = this;
	
	this.onAddToMap = function (map, parentContainer) {
		me_.map_ = map;
		var div = YMaps.jQuery('<div class="YMaps-placemark YMaps-Default YMaps-cursor-pointer">');
		div.css({
			'z-index': YMaps.ZIndex.Overlay,
			width: me_.width_,
			height: me_.height_,
			'text-align': 'center',
			'line-height': me_.height_.toString() + 'px',
			'font-size': '10px',
			color: me_.textColor_ ? me_.textColor_ : '#000'
		});
		
		div.text(me_.count_);
		
		if (document.all) {
			div.css({
				filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="' + this.url_ + '")'
			});
		} else {
			div.css({
				background: "url(" + me_.url_ + ")"
			});
		}
		
		div.appendTo(parentContainer);
		me_.div_ = div;
		
		div.click(function (e) {
		  var pos = map.converter.coordinatesToMapPixels(point);
		  
		  var sw = new YMaps.Point(pos.x - padding, pos.y + padding);
          sw = map.converter.mapPixelsToCoordinates(sw);
          
          var ne = new YMaps.Point(pos.x + padding, pos.y - padding);
          ne = map.converter.mapPixelsToCoordinates(ne);
           
		  map.setBounds(new YMaps.GeoBounds(sw, ne));
        });

		me_.onMapUpdate();
	};
	
	this.onRemoveFromMap = function () {
		if (me_.div_.parent()) {
			me_.div_.remove();
		}
	};

	this.onMapUpdate = function () {
		var position = me_.map_.converter.coordinatesToMapPixels(point);
		
		position.x -= parseInt(me_.width_ / 2, 10);
		position.y -= parseInt(me_.height_ / 2, 10);
		
		me_.div_.css({
			left: position.x,
			top:  position.y
		});
		
		me_.div_.text(me_.count_);
	};
	
	this.setCount = function (count) {
		me_.count_ = count;
	};
	
	this.show = function () {
		me_.div_.css('display', '');
	};
	
	this.hide = function () {
		me_.div_.css('display', 'none');
	};
	
	this.isHidden = function () {
		return me_.div_.css('display') === "none";
	};
};

YMaps.Placemark.prototype.show = function () {
	if (this._$iconContainer) { // dirty hacking...
		this._$iconContainer.css('display', '');
	}
};

YMaps.Placemark.prototype.hide = function () {
	if (this._$iconContainer) { // dirty hacking...
		this._$iconContainer.css('display', 'none');
	}
};

YMaps.Placemark.prototype.isHidden = function () {
	if (this._$iconContainer) {
		return this._$iconContainer.css('display') == 'none';
	} else {
		return true;
	}
};
