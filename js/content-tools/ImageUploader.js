function imageUploader(dialog) {
    let image, xhr, xhrComplete, xhrProgress;

    dialog.addEventListener('imageuploader.fileready', function (ev) {

        // Upload a file to the server
        let formData;
        let file = ev.detail().file;

        // Define functions to handle upload progress and completion
        xhrProgress = function (ev) {
            // Set the progress for the upload
            dialog.progress((ev.loaded / ev.total) * 100);
        };

        xhrComplete = function (ev) {
            let response;

            // Check the request is complete
            if (ev.target.readyState != 4) {
                return;
            }

            // Clear the request
            xhr = null;
            xhrProgress = null;
            xhrComplete = null;

            // Handle the result of the upload
            if (parseInt(ev.target.status) === 200) {
                // Store the image details
                console.log(ev.target.responseText);

                // Unpack the response (from JSON)
                response = JSON.parse(ev.target.responseText);
                image = {
                    size: response.body.image.size,
                    url: response.body.image.draft
                };

                // Populate the dialog
                dialog.populate(image.url, image.size);

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        };

        // Set the dialog state to uploading and reset the progress bar to 0
        dialog.state('uploading');
        dialog.progress(0);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('image', file);

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', xhrProgress);
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', 'api/upload/contenttootls', true);
        xhr.send(formData);
    });



    dialog.addEventListener('imageuploader.save', function () {
        let crop, cropRegion, formData;

        // Define a function to handle the request completion
        xhrComplete = function (ev) {
            // Check the request is complete
            if (ev.target.readyState !== 4) {
                return;
            }

            // Clear the request
            xhr = null;
            xhrComplete = null;

            // Free the dialog from its busy state
            dialog.busy(false);

            // Handle the result of the rotation
            if (parseInt(ev.target.status) === 200) {
                console.log(ev.target.responseText);

                // Unpack the response (from JSON)
                let response = JSON.parse(ev.target.responseText);
                // Trigger the save event against the dialog with details of the
                // image to be inserted.
                dialog.save(
                    response.body.url,
                    response.body.size,
                    {
                        'alt': response.body.alt,
                        'data-ce-max-width': response.body.width
                    });

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        };

        // Set the dialog to busy while the rotate is performed
        dialog.busy(true);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('url', image.url);

        // Set the width of the image when it's inserted, this is a default
        // the user will be able to resize the image afterwards.
        formData.append('width', 600);

        // Check if a crop region has been defined by the user
        if (dialog.cropRegion()) {
            formData.append('crop', dialog.cropRegion());
        }

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', 'api/upload/contenttootls/insert', true);
        xhr.send(formData);
    });

    function rotateImage(direction) {
        // Request a rotated version of the image from the server
        let formData;

        // Define a function to handle the request completion
        xhrComplete = function (ev) {
            let response;

            // Check the request is complete
            if (ev.target.readyState != 4) {
                return;
            }

            // Clear the request
            xhr = null;
            xhrComplete = null;

            // Free the dialog from its busy state
            dialog.busy(false);

            // Handle the result of the rotation
            if (parseInt(ev.target.status) == 200) {
                // Unpack the response (from JSON)
                console.log(ev.target.responseText);
                response = JSON.parse(ev.target.responseText);

                // Store the image details (use fake param to force refresh)
                image = {
                    size: response.body.size,
                    url: image.url.split('?')[0] + '?_ignore=' + Date.now()
                };

                // Populate the dialog
                dialog.populate(image.url, image.size);

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        };

        // Set the dialog to busy while the rotate is performed
        dialog.busy(true);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('url', image.url.split('?')[0]);
        formData.append('direction', direction);

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', 'api/upload/contenttools/rotate', true);
        xhr.send(formData);
    }

    dialog.addEventListener('imageuploader.rotateccw', function () {
        rotateImage(-1);
    });

    dialog.addEventListener('imageuploader.rotatecw', function () {
        rotateImage(1);
    });
}