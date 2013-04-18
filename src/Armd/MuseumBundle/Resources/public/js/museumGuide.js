var armdMuseumGuide = {

    init: function () {
        // filter
        $('body').on('click', '.ui-selectgroup-list[aria-labelledby="ui-filter-city"] a, .ui-selectgroup-list[aria-labelledby="ui-filter-museum"] a',
            function (event) {
                $('#museums-filter-form').submit();
            });

        
        if(/*@cc_on!@*/false){
            document.documentMode<= 8 ? $('#pdf-lister').hide() : true;         // disable pdf-viewer for IE <= 8
            document.documentMode<= 10 ? $('#pdf-fullscreen').hide() : true;    // disable fullscreen for IE <= 10 
        }
    },
    
    initPdf : function (url) {
        PDFJS.disableWorker = true;
        pdfDoc = null;
        PDFJS.getDocument(url).then(function getPdfHelloWorld (_pdfDoc) {
            pdfDoc = _pdfDoc;
            pdfViewer.init();
            $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', pdfViewer.onFullscreen);
        });
    }

};

var pdfViewer = {
    scale : 1.0,
    pageWidth: 360,
    pageHeight: 525,
    pageNum : 1,
    page : { left: null, right : null },
    _curPage : null,
    init : function() {
            var canvas = document.getElementById('pdf-canvas'),
                canvasH = document.getElementById('pdf-canvas-h');
            this.page.hidden = {canvas : canvasH, ctx: canvasH.getContext('2d')};
            var ctx = canvas.getContext('2d');
            this.page.left = { canvas : canvas, ctx : ctx, dx: 0, dy: 0 };
            this.page.right = { canvas : canvas, ctx : ctx, dx: this.pageWidth, dy: 0 };
            this.updateDimensions();
            this.render();
    },
    updateDimensions : function () {
            var w = this.pageWidth * this.scale,
                h = this.pageHeight * this.scale;
            if(w && w !== this.page.hidden.canvas.width) {
                this.page.hidden.canvas.width = w;
                this.page.left.canvas.width = w * 2;
            }
            if(h && h !== this.page.hidden.canvas.height) {
                this.page.hidden.canvas.height = h;
                this.page.left.canvas.height = h;
            }
    },
    render : function () {
            this.renderPage(this.pageNum, this.page.left);
    },
    renderPage : function (num, page) {
            this.enableButtons();
            if (num >= 1 && num <= pdfDoc.numPages) {
                this.disableButtons();
                pdfDoc.getPage(num).then(this.renderInner);
            } else {
                page.ctx.clearRect(
                    page.dx*this.scale, page.dy*this.scale, 
                    this.page.hidden.canvas.width + page.dx*this.scale, this.page.hidden.canvas.height + page.dy*this.scale
                );
                if(num === this.pageNum) {
                    this.renderPage(num + 1, this.page.right);
                }
            }
    },
    renderInner : function (page) {
        pageRendering = page.render({
            canvasContext: pdfViewer.page.hidden.ctx, 
            viewport: page.getViewport(pdfViewer.page.hidden.canvas.width / page.getViewport(1.0).width)
        });
        pageRendering.onData(function(){ 
            var isRight = page.pageNumber === pdfViewer.pageNum + 1,
                cpage = isRight ? pdfViewer.page.right : pdfViewer.page.left;
            if(isRight) {
                cpage.ctx.translate(pdfViewer.page.hidden.canvas.width, 0);
                cpage.ctx.drawImage(pdfViewer.page.hidden.canvas, 0, 0);
                cpage.ctx.translate(-pdfViewer.page.hidden.canvas.width, 0);
                pdfViewer.enableButtons();
            } else {
                cpage.ctx.drawImage(pdfViewer.page.hidden.canvas, 0, 0);
                return pdfViewer.renderPage(pdfViewer.pageNum + 1, pdfViewer.page.right);
            }
        });
    },
    goNext : function () {
        if (this.pageNum >= pdfDoc.numPages - 1)
            return;
        this.disableButtons();
        this.pageNum += 2;
        this.render();
    },
    goPrev : function () {
        if (this.pageNum <= 1)
            return;
        this.disableButtons();
        this.pageNum -= 2;
        this.render();
    },
    disableButtons : function () {
        $('#pdf-prev').attr('disabled', 'disabled');
        $('#pdf-next').attr('disabled', 'disabled');
    },
    enableButtons : function () {
        $('#pdf-prev').removeAttr('disabled');
        $('#pdf-next').removeAttr('disabled');
    },
    zoomIn: function (scale) {
        this.scale = Math.max( 0.25, Math.min( 4.0, Math.ceil((null == scale ? this.scale + 0.20 : scale).toFixed(2) * 10) / 10));
        this.disableButtons();
        this.updateDimensions();
        return this.render();
    },
    zoomOut: function (scale) {
        this.scale = Math.max( 0.25, Math.min( 4.0, Math.ceil((null == scale ? this.scale - 0.20 : scale).toFixed(2) * 10) / 10));
        this.disableButtons();
        this.updateDimensions();
        return this.render();
    },
    fullscreen: function pdfViewFullscreen() {
        var isFullscreen = document.fullscreenElement || document.mozFullScreen || document.webkitIsFullScreen;
        if (isFullscreen) {
            return true;
        } 

        var wrapper = document.getElementById('pdf-viewerContainer');
        if (document.documentElement.requestFullscreen) {
            wrapper.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            wrapper.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
            wrapper.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        } else {
            return false;
        }
        return true;
    },
    onFullscreen: function() {
        if (pdfViewer.isFullscreen === true) {
            pdfViewer.isFullscreen = false;
            pdfViewer.zoomOut(pdfViewer.previousScale);
            $('#pdf-viewer').css('height', pdfViewer.previous.viewerH);
            $('.pdf-btn').css('position', 'relative');
            $('div.pdf-btn').css('left', '');
        } else {
            pdfViewer.isFullscreen = true;
            pdfViewer.previousScale = pdfViewer.scale;
            pdfViewer.zoomIn(2.0);
            pdfViewer.previous = {
                viewerH: $('#pdf-viewer').css('height')
            };
            $('#pdf-viewer').css('height', '100%');
            $('.pdf-btn').css('position', 'fixed');
            $('div.pdf-btn').css('left', '47%');
        }
        return true;
    }
};
