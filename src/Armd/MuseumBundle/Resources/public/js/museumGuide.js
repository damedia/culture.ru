var armdMuseumGuide = {

    init: function () {
    },

    initPdf : function (url) {
        PDFJS.disableWorker = true;
        pdfDoc = null;
        pdfViewer = {
            scale : 0.5,
            pageNum : 1,
            page : { left: null, right : null },
            _curPage : null,
            init : function() {
                var canvas = document.getElementById('pdf-canvas'),
                    canvasH = document.getElementById('pdf-canvas-h');
                canvasH.width = 350;
                canvasH.height = 525;
                canvas.width = canvasH.width * 2;
                canvas.height = canvasH.height;
                var ctx = canvas.getContext('2d');
                this.page.left = { canvas : canvas, ctx : ctx, dx: 0, dy: 0 };
                this.page.right = { canvas : canvas, ctx : ctx, dx: canvasH.width, dy: 0 };
                this.page.hidden = {canvas : canvasH, ctx: canvasH.getContext('2d')};
                this.render();
            },
            render : function () {
                this.renderPage(this.pageNum, this.page.left);
            },
            renderPage : function (num, page) {
                pdfViewer.enableButtons();
                if (num >= 1 && num <= pdfDoc.numPages) {
                    pdfViewer.disableButtons();
                    pdfDoc.getPage(num).then(this.renderInner);
                } else {
                    page.ctx.clearRect(page.dx, page.dy, page.canvas.width/2 + page.dx, page.canvas.height + page.dy);
                    if(num === pdfViewer.pageNum) {
                        this.renderPage(num + 1, this.page.right);
                    }
                }
            },
            renderInner : function (page) {
                pageRendering = page.render({
                    canvasContext: pdfViewer.page.hidden.ctx, 
                    viewport: page.getViewport( pdfViewer.scale)
                });
                pageRendering.onData(function(){ 
                    var isRight = page.pageNumber === pdfViewer.pageNum + 1,
                        cpage = isRight ? pdfViewer.page.right : pdfViewer.page.left;
                    if(isRight) {
                        cpage.ctx.translate(cpage.dx, cpage.dy);
                        cpage.ctx.drawImage(pdfViewer.page.hidden.canvas, 0, 0);
                        cpage.ctx.translate(-cpage.dx, -cpage.dy);
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
            }
        };

        PDFJS.getDocument(url).then(function getPdfHelloWorld (_pdfDoc) {
            pdfDoc = _pdfDoc;
            pdfViewer.init();
        });
    }

};
