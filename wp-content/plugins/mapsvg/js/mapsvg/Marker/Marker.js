import { MapSVG } from "../Core/globals.js";
import { MapObject } from "../MapObject/MapObject.js";
import { SVGPoint, ScreenPoint } from "../Location/Location.js";
import { ViewBox } from "../Map/MapOptionsInterface.js";
const $ = jQuery;
export class Marker extends MapObject {
    constructor(params) {
        super(null, params.mapsvg);
        this.update = function (data) {
            for (var key in data) {
                var setter = 'set' + MapSVG.ucfirst(key);
                if (setter in this)
                    this[setter](data[key]);
            }
        };
        this.src = params.location.getMarkerImageUrl();
        var img = $('<img src="' + this.src + '" />').addClass('mapsvg-marker');
        this.element = img[0];
        this.location = params.location;
        this.location.marker = this;
        this.mapsvg = params.mapsvg;
        params.object && this.setObject(params.object);
        if (params.width && params.height) {
            this.width = params.width;
            this.height = params.height;
        }
        this.setId(this.mapsvg.markerId());
        this.svgPoint = this.location.svgPoint || this.mapsvg.convertGeoToSVG(this.location.geoPoint);
        this.setImage(this.src);
        this.setAltAttr();
    }
    setId(id) {
        MapObject.prototype.setId.call(this, id);
        this.mapsvg.markers.reindex();
    }
    ;
    getBBox() {
        var bbox = { x: this.svgPoint.x, y: this.svgPoint.y, width: this.width / this.mapsvg.scale, height: this.height / this.mapsvg.scale };
        bbox = $.extend(true, {}, bbox);
        return new ViewBox(bbox);
    }
    ;
    getOptions() {
        var o = {
            id: this.id,
            src: this.src,
            svgPoint: this.svgPoint,
            geoPoint: this.geoPoint
        };
        $.each(o, function (key, val) {
            if (val == undefined) {
                delete o[key];
            }
        });
        return o;
    }
    ;
    setImage(src) {
        if (!src)
            return false;
        var _this = this;
        src = MapSVG.safeURL(src);
        var img = new Image();
        var marker = this;
        this.src = src;
        if (marker.element.getAttribute('src') !== 'src') {
            marker.element.setAttribute('src', src);
        }
        img.onload = function () {
            marker.width = this.width;
            marker.height = this.height;
            _this.adjustScreenPosition();
        };
        img.src = src;
        if (this.location) {
            this.location.setImage(src);
        }
    }
    ;
    setAltAttr() {
        var marker = this;
        marker.altAttr = (typeof marker.object != 'undefined') && (typeof marker.object.title != 'undefined') && (marker.object.title !== '') ? marker.object.title : marker.id;
        marker.element.setAttribute('alt', marker.altAttr);
    }
    ;
    setPoint(svgPoint) {
        this.svgPoint = svgPoint;
        if (this.location) {
            this.location.setSvgPoint(this.svgPoint);
        }
        if (this.mapsvg.mapIsGeo) {
            this.geoPoint = this.mapsvg.convertSVGToGeo(this.svgPoint);
            this.location.setGeoPoint(this.geoPoint);
        }
        this.adjustScreenPosition();
        this.events.trigger('change');
    }
    ;
    adjustScreenPosition() {
        let pos = this.mapsvg.convertSVGToPixel(this.svgPoint);
        pos.x -= this.width / 2;
        pos.y -= !this.centered ? this.height : this.height / 2;
        this.setScreenPosition(pos.x, pos.y);
    }
    ;
    moveSrceenPositionBy(deltaX, deltaY) {
        let oldPos = this.screenPoint, x = oldPos.x - deltaX, y = oldPos.y - deltaY;
        this.setScreenPosition(x, y);
    }
    ;
    setScreenPosition(x, y) {
        if (this.screenPoint instanceof ScreenPoint) {
            this.screenPoint.x = x;
            this.screenPoint.y = y;
        }
        else {
            this.screenPoint = new ScreenPoint(x, y);
        }
        if (this.inViewBox()) {
            this.element.style.transform = 'translate(' + x + 'px,' + y + 'px)';
            this.adjustLabelScreenPosition();
            this.updateVisibility(true);
        }
        else {
            this.updateVisibility(false);
        }
    }
    ;
    adjustLabelScreenPosition() {
        if (this.textLabel) {
            let markerPos = this.screenPoint, x = Math.round(markerPos.x + this.width / 2 - $(this.textLabel).outerWidth() / 2), y = Math.round(markerPos.y - $(this.textLabel).outerHeight());
            this.setLabelScreenPosition(x, y);
        }
    }
    ;
    moveLabelScreenPositionBy(deltaX, deltaY) {
        if (this.textLabel) {
            let markerPos = this.screenPoint, x = Math.round(markerPos.x + this.width / 2 - $(this.textLabel).outerWidth() / 2 - deltaX), y = Math.round(markerPos.y - $(this.textLabel).outerHeight() - deltaY);
            this.setLabelScreenPosition(x, y);
        }
    }
    setLabelScreenPosition(x, y) {
        if (this.textLabel) {
            this.textLabel.style.transform = 'translate(' + x + 'px,' + y + 'px)';
        }
    }
    inViewBox() {
        let x = this.screenPoint.x, y = this.screenPoint.y, mapFullWidth = this.mapsvg.containers.map.offsetWidth, mapFullHeight = this.mapsvg.containers.map.offsetHeight;
        return (x - this.width / 2 < mapFullWidth) && (x + this.width / 2 > 0) && (y - this.height / 2 < mapFullHeight) && (y + this.height / 2 > 0);
    }
    updateVisibility(visible) {
        if (visible === true) {
            this.visible = true;
            this.element.classList.remove('mapsvg-out-of-sight');
            if (this.textLabel) {
                this.textLabel.classList.remove('mapsvg-out-of-sight');
            }
        }
        else {
            this.visible = false;
            this.element.classList.add('mapsvg-out-of-sight');
            if (this.textLabel) {
                this.textLabel.classList.add('mapsvg-out-of-sight');
            }
        }
    }
    drag(startCoords, scale, endCallback, clickCallback) {
        var _this = this;
        this.svgPointBeforeDrag = new SVGPoint(this.svgPoint.x, this.svgPoint.y);
        this.dragging = true;
        $('body').on('mousemove.drag.mapsvg', function (e) {
            e.preventDefault();
            $(_this.mapsvg.containers.map).addClass('no-transitions');
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            var newSvgPoint = new SVGPoint(_this.svgPointBeforeDrag.x + dx / scale, _this.svgPointBeforeDrag.y + dy / scale);
            _this.setPoint(newSvgPoint);
        });
        $('body').on('mouseup.drag.mapsvg', function (e) {
            e.preventDefault();
            _this.undrag();
            var mouseNew = MapSVG.mouseCoords(e);
            var dx = mouseNew.x - startCoords.x;
            var dy = mouseNew.y - startCoords.y;
            var newSvgPoint = new SVGPoint(_this.svgPointBeforeDrag.x + dx / scale, _this.svgPointBeforeDrag.y + dy / scale);
            _this.setPoint(newSvgPoint);
            if (_this.mapsvg.isGeo()) {
                _this.geoPoint = _this.mapsvg.convertSVGToGeo(newSvgPoint);
            }
            endCallback && endCallback.call(_this);
            if (_this.svgPointBeforeDrag.x == _this.svgPoint.x && _this.svgPointBeforeDrag.y == _this.svgPoint.y)
                clickCallback && clickCallback.call(_this);
        });
    }
    ;
    undrag() {
        this.dragging = false;
        $('body').off('.drag.mapsvg');
        $(this.mapsvg.containers.map).removeClass('no-transitions');
    }
    ;
    delete() {
        if (this.textLabel) {
            this.textLabel.remove();
            this.textLabel = null;
        }
        $(this.element).empty().remove();
        this.mapsvg.markerDelete(this);
    }
    ;
    setObject(obj) {
        this.object = obj;
        $(this.element).attr('data-object-id', this.object.id);
    }
    ;
    hide() {
        $(this.element).addClass('mapsvg-marker-hidden');
        if (this.textLabel) {
            $(this.textLabel).hide();
        }
    }
    ;
    show() {
        $(this.element).removeClass('mapsvg-marker-hidden');
        if (this.textLabel) {
            $(this.textLabel).show();
        }
    }
    ;
    highlight() {
        $(this.element).addClass('mapsvg-marker-hover');
    }
    ;
    unhighlight() {
        $(this.element).removeClass('mapsvg-marker-hover');
    }
    ;
    select() {
        this.selected = true;
        $(this.element).addClass('mapsvg-marker-active');
    }
    ;
    deselect() {
        this.selected = false;
        $(this.element).removeClass('mapsvg-marker-active');
    }
    ;
    getData() {
        return this.object;
    }
}
//# sourceMappingURL=Marker.js.map