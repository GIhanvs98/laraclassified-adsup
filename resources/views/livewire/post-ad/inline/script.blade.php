<div>

    @script
    
        <script type="module">

            document.addEventListener('livewire:initialized', () => {

                @this.on('changeLocation', (event) => {

                    $('#changeLocation').click();
                });

                $(function() {

                    tinymce.init({
                        selector: '#content', 
                        plugins: "emoticons autoresize", 
                        // toolbar: "undo redo | bold italic underline | subscript superscript | bullist numlist | link emoticons | removeformat",
                        toolbar: "undo redo | emoticons", 
                        toolbar_location: "top", 
                        menubar: false, 
                        statusbar: false,
                        paste_as_text: true,
                        min_height: 200, 
                        forced_root_block : false,
                        setup: function(editor) {
                            editor.on('init change', function() {
                                editor.save();
                            });
                            editor.on('init', function(e) {

                                let originalString = $wire.$get('content');

                                let formattedContent = originalString.replace(/\n/g, '<br>');

                                editor.setContent(formattedContent);

                            });
                            editor.on('change', function(e) {
                                
                                let originalString = editor.getContent();

                                let formattedContent = originalString.replace(/<br>/g, '\n');

                                let encodedString = he.encode(formattedContent);

                                $wire.$set('content', encodedString);
                            
                            });
                        }
                    });

                });

                // Custom Image uploader

                const pixelHatch = new PixelHatch({
                    fieldName: "images",
                    allowSameFile: false,
                    maxImages: $wire.$get('imagesLimit'),
                    maxFileSize: 2048,
                    allowedExtensions: ["png", "jpeg", "jpg"],
                    imageInputSelector: "#image-input",
                    imagePreviewSelector: "#image-preview",
                    styles: {
                        previewContainer: ["relative", "m-2", "group"],
                        previewImage: ["w-24", "h-24", "object-cover", "rounded", "hover:shadow-lg", "hover:shadow-blue-500/50", "ring-1", "ring-gray-200", "hover:ring-2", "hover:ring-blue-500/50"],
                        deleteButton: ["absolute", "top-1", "right-1", "text-white", "rounded"],
                    },
                    existingImages: $wire.$get('localImages'),
                    imagesOrderKeywords: { new: "new", old: "old" },
                    onInit: function () {

                        const pixelHatchThis = this;
                                
                        // Sortability

                        $(pixelHatchThis.imagePreviewSelector).sortable({
                            forcePlaceholderSize:true,
                            cancel: ".not-sortable", 
                            containment: "parent",
                            placeholder: "sortable-placeholder",
                            update: function(event, ui) {

                                pixelHatchThis.sort();
                            }
                        });

                        $(pixelHatchThis.imagePreviewSelector).disableSelection();

                        // Custom configurations

                        document.querySelectorAll(".image-upload-button").forEach(element => {
                        
                            element.addEventListener('click', () => {
                                
                                document.querySelector(pixelHatchThis.imageInputSelector).click();
                            });
                        });

                        // Setting up dynamic data

                        if(pixelHatchThis.maxImages == -1){
                            
                            document.querySelector('#max-images').innerHTML = `Add images`;

                        }else{

                            document.querySelector('#max-images').innerHTML = `Add upto ${pixelHatchThis.maxImages} images`;
                        }
                        
                        // Upload button click

                        const uploadButton = document.querySelector('#save-post');

                        uploadButton.addEventListener('click', function(){

                            $wire.$set("imagesOrder", pixelHatchThis.imagesOrder);

                            // $wire.$set("localImages", pixelHatchThis.localImages);

                            /*
                            $wire.$uploadMultiple("images", 
                                pixelHatchThis.formData.getAll(pixelHatchThis.fieldName + "[]"), 
                                function(){
                                    $wire.$call('save');
                                }, 
                                function(){
                                    // console.log("Submited -> Image upload process failed!");
                                }
                            );     
                            */

                            $wire.$call('save');

                        });

                        // Dropzone

                        const dropZone = document.getElementById('drop-zone');

                        dropZone.addEventListener('dragover', function (e) {
                            e.preventDefault();
                        });

                        dropZone.addEventListener('dragleave', function () {
                            // You can add some visual feedback here if needed
                        });

                        dropZone.addEventListener('drop', function (e) {
                            e.preventDefault();

                            const files = e.dataTransfer.files;
                            handleFiles(files);
                        });

                        function handleFiles(files) {
                            const fileInput = document.getElementById('image-input');
                            fileInput.files = files;

                            pixelHatchThis.handleImageUpload();
                        }

                    },
                    onChange: function () {

                        const pixelHatchThis = this;

                        document.querySelectorAll('.images-upload-error').forEach(element => {
                            element.classList.remove('invisible');
                        });

                        const imageUploadIntroElem = document.getElementById('image-upload-intro');

                        if(pixelHatchThis.length >= 1){

                            imageUploadIntroElem.style.display = 'none';

                        }else{

                            imageUploadIntroElem.style.display = 'flex';
                        }

                        const totalImagesElem = document.querySelector('#images-count');

                        $wire.$set("imagesCount", pixelHatchThis.length);

                        if(pixelHatchThis.length == 1){

                            totalImagesElem.innerHTML = `${pixelHatchThis.length} image`;
                        }else{

                            totalImagesElem.innerHTML = `${pixelHatchThis.length} images`;
                        }

                        $wire.$uploadMultiple("images", pixelHatchThis.formData.getAll(this.fieldName + "[]"),
                            function(){ /*console.log("Images upload completed : for validation");*/ },
                            function(){ /*console.info("Image upload process failed : for validation!"); */}
                        );
                    },
                    onSort: function () {

                        $wire.$set("imagesOrder", this.imagesOrder);
                    },
                    onDelete: function () {
                                        
                        $wire.$set("localImages", this.existingImages);
                    }
                });

                const contact_numbers = $wire.$get('contact_numbers');

                let contact_numbers_counters = [];

                Object.entries(contact_numbers).forEach(function(currentCN, index) {

                    contact_numbers_counters[index] = new Interval({
                        counter: `#counter_${currentCN[1].id}`,
                        delay: 1000, // miliseconds - define the time taken to switch to next digit - default 1 second
                        duration: 2, // minutes - total duration
                        onStart: function () {
                            // console.log("When started the interval");
                        },
                        onEnd: function () {
                            // console.log("Ended interval:- ", `contact_numbers.${currentCN[1].id}.states.countdown`);
                            $wire.$set(`contact_numbers.${currentCN[1].id}.states.countdown`, false);
                        },
                        onRestart: function () {
                            // console.log("When restarted the interval");
                        },
                    });

                    $wire.on(`start_timer_${currentCN[1].id}` , () => {
                        contact_numbers_counters[index].start();
                    });

                    $wire.on(`restart_timer_${currentCN[1].id}` , () => {
                        contact_numbers_counters[index].restart();
                    });

                });

            });

        </script>

    @endscript

</div>