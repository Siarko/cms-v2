window.addEventListener('load', function () {
    let editor;

    /*ContentTools.StylePalette.add([
        new ContentTools.Style('Author', 'author', ['p']),
        new ContentTools.Style('Jakiś quote','jakisquote',['blockquote'])
    ]);*/

    editor = ContentTools.EditorApp.get();
    editor.init('*[data-editable]', 'data-name');

    ContentTools.StylePalette.add([
        new ContentTools.Style('Trochę z lewej', 'aBitLeft'),
        new ContentTools.Style('Katalog stron', 'subpageCatalogue', 'x-dynamic')
    ]);

    ContentEdit.LINE_ENDINGS = "";
    ContentEdit.LANGUAGE = "pl";

    require({
        url: 'content-tools/ImageUploader',
        onload: function () { //load image uploader from file
            ContentTools.IMAGE_UPLOADER = imageUploader;
        }
    });

    editor.addEventListener('saved', function (ev) {
        if(DynamicControls){
            DynamicControls.scan();
        }
        let name, payload, regions, xhr;

        // Check that something changed
        regions = ev.detail().regions;
        if (Object.keys(regions).length === 0) {
            return;
        }

        // Set the editor as busy while we save our changes
        this.busy(true);

        // Collect the contents of each region into a FormData instance
        payload = new FormData();
        for (name in regions) {
            if (regions.hasOwnProperty(name)) {
                let v = regions[name];
                payload.append(name, v);
            }
        }

        // Send the update content to the server to be saved
        function onStateChange(ev) {
            // Check if the request is finished
            if (ev.target.readyState == 4) {
                editor.busy(false);
                if (ev.target.status == '200') {
                    let a = ev.target.response;
                    console.log(a);
                    // Save was successful, notify the user with a flash
                    new ContentTools.FlashUI('ok');
                } else {
                    // Save failed, notify the user with a flash
                    new ContentTools.FlashUI('no');
                }
            }
        }

        xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', onStateChange);
        xhr.open('POST', 'api/page/content/change?fake_lang='+LANGUAGE);
        xhr.send(payload);
    });
});