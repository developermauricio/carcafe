import { MapObject } from "../MapObject/MapObject.js";
import { ViewBox } from "../Map/MapOptionsInterface";
import { ScreenPoint } from "../Location/Location";
const $ = jQuery;
export class MarkerCluster extends MapObject {
    constructor(options, mapsvg) {
        super(null, mapsvg);
        this.svgPoint = options.svgPoint;
        this.cellX = options.cellX;
        this.cellY = options.cellY;
        this.markers = options.markers || [];
        this.cellSize = 50;
        this.width = 30;
        var _this = this;
        this.elem = $('<div class="mapsvg-marker-cluster">' + this.markers.length + '</div>')[0];
        $(this.elem).data("cluster", this);
        if (this.markers.length < 2) {
            $(this.elem).hide();
        }
        this.adjustScreenPosition();
    }
    addMarker(marker) {
        this.markers.push(marker);
        if (this.markers.length > 1) {
            if (this.markers.length === 2) {
                $(this.elem).show();
            }
            if (this.markers.length === 2) {
                var x = this.markers.map(function (m) {
                    return m.svgPoint.x;
                });
                this.min_x = Math.min.apply(null, x);
                this.max_x = Math.max.apply(null, x);
                var y = this.markers.map(function (m) {
                    return m.svgPoint.y;
                });
                this.min_y = Math.min.apply(null, y);
                this.max_y = Math.max.apply(null, y);
                this.svgPoint.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.svgPoint.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
            if (this.markers.length > 2) {
                if (marker.svgPoint.x < this.min_x) {
                    this.min_x = marker.svgPoint.x;
                }
                else if (marker.svgPoint.x > this.max_x) {
                    this.max_x = marker.svgPoint.x;
                }
                if (marker.svgPoint.y < this.min_y) {
                    this.min_y = marker.svgPoint.y;
                }
                else if (marker.svgPoint.x > this.max_x) {
                    this.max_y = marker.svgPoint.y;
                }
                this.svgPoint.x = this.min_x + ((this.max_x - this.min_x) / 2);
                this.svgPoint.y = this.min_y + ((this.max_y - this.min_y) / 2);
            }
        }
        else {
            this.svgPoint.x = marker.svgPoint.x;
            this.svgPoint.y = marker.svgPoint.y;
        }
        $(this.elem).text(this.markers.length);
        this.adjustScreenPosition();
    }
    canTakeMarker(marker) {
        var _this = this;
        var screenPoint = _this.mapsvg.convertSVGToPixel(marker.svgPoint);
        return (this.cellX === Math.ceil(screenPoint.x / this.cellSize)
            &&
                this.cellY === Math.ceil(screenPoint.y / this.cellSize));
    }
    destroy() {
        this.markers = null;
        $(this.elem).remove();
    }
    adjustScreenPosition() {
        let pos = this.mapsvg.convertSVGToPixel(this.svgPoint);
        pos.x -= this.width / 2;
        pos.y -= this.width / 2;
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
            this.elem.style.transform = 'translate(' + x + 'px,' + y + 'px)';
            this.updateVisibility(true);
        }
        else {
            this.updateVisibility(false);
        }
    }
    ;
    inViewBox() {
        let x = this.screenPoint.x, y = this.screenPoint.y, mapFullWidth = this.mapsvg.containers.map.offsetWidth, mapFullHeight = this.mapsvg.containers.map.offsetHeight;
        return (x - this.width / 2 < mapFullWidth) && (x + this.width / 2 > 0) && (y - this.width / 2 < mapFullHeight) && (y + this.width / 2 > 0);
    }
    updateVisibility(visible) {
        if (visible === true) {
            this.visible = true;
            this.elem.classList.remove('mapsvg-out-of-sight');
        }
        else {
            this.visible = false;
            this.elem.classList.add('mapsvg-out-of-sight');
        }
    }
    getBBox() {
        var bbox = {
            x: this.svgPoint.x,
            y: this.svgPoint.y,
            width: this.cellSize / this.mapsvg.getScale(),
            height: this.cellSize / this.mapsvg.getScale()
        };
        bbox = $.extend(true, {}, bbox);
        return new ViewBox(bbox.x, bbox.y, bbox.width, bbox.height);
    }
    ;
    getData() {
        return this.markers.map(m => m.object);
    }
}
//# sourceMappingURL=MarkerCluster.js.map