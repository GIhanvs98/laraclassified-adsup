class PixelHatch{

    constructor(config) {
        
        // Configuration options
        this.fieldName = config.fieldName || "images";

        this.allowSameFile = config.allowSameFile || false;

        this.maxImages = config.maxImages || -1;

        this.maxFileSize = config.maxFileSize || 2048;

        this.allowedExtensions = config.allowedExtensions || ["png", "jpeg", "jpg"];

        this.imageInputSelector = config.imageInputSelector || "name=[images]";

        this.imagePreviewSelector = config.imagePreviewSelector || "images-preview";

        this.imageInput = document.querySelector(config.imageInputSelector);

        this.imagePreview = document.querySelector(config.imagePreviewSelector);
        
        this.styles = config.styles || {
            previewContainer: ["relative", "m-2", "group"],
            previewImage: ["w-24", "h-24", "object-cover"],
            deleteButton: ["absolute", "top-1", "right-1", "text-white", "rounded"],
        };

        this.deleteButtonHtml = config.deleteButtonHtml || `<svg width="18" height="18" viewBox="0 0 18 18" class="cursor-pointer"><g fill="none" fill-rule="evenodd"><path fill-opacity="0" fill="red" d="M0 0h18v18H0z"></path><g transform="translate(1 1)"><circle fill="#D95E46" cx="8" cy="8" r="8"></circle><rect fill="#FFF" x="4" y="7" width="8" height="2" rx="0.5"></rect></g></g></svg>`;
        
        this.existingImages = config.existingImages || [];

        this.length = 0;
        
        this.imagesOrder = [];
        
        this.imagesOrderKeywords = { old: config.imagesOrderKeywords.old || "old", new: config.imagesOrderKeywords.new || "new" };

        // Creating new form field;
        this.formData = new FormData();

        // Attach event listeners
        this.imageInput.addEventListener("change", this.handleImageUpload.bind(this));
        
        if (typeof config.onInit === "function") {

            this.onInit = config.onInit;
        } else {

            this.onInit = function () { };
        }
        
        if (typeof config.onChange === "function") {

            this.onChange = config.onChange;
        } else {

            this.onChange = function () { };
        }
        
        if (typeof config.onSort === "function") {

            this.onSort = config.onSort;
        } else {

            this.onSort = function () { };
        }
        
        if (typeof config.onLoadExisting === "function") {

            this.onLoadExisting = config.onLoadExisting;
        } else {

            this.onLoadExisting = function () { };
        }
        
        if (typeof config.onValidationInit === "function") {

            this.onValidationInit = config.onValidationInit;
        } else {

            this.onValidationInit = function () { };
        }
        
        if (typeof config.onDelete === "function") {

            this.onDelete = config.onDelete;
        } else {

            this.onDelete = function () { };
        }
        
        this.onInit(); // Method to rn when initialize the plugin.

        this.initExistingImages();

    }

    initExistingImages() {

        this.existingImages.forEach((image, index) => {

            if (this.length <= this.maxImages) {
                    
                const previewContainer = this.createPreviewContainer(image.absolute_url, index);
                
                this.imagePreview.appendChild(previewContainer);
                
                this.imagesOrder.push({ type: this.imagesOrderKeywords.old, key: index });

                this.length += 1;
            }
            
        });

        this.onChange(); // Run after file selection, sort or delete actions are completed. This can be even if all images select, few selected or notanything selectd.

        this.onLoadExisting(); // When existing images laded.
    }

    handleImageUpload() {
        
        const files = this.imageInput.files;

        Array.from(files).forEach((file, index) => {

            if (this.imagesValidation(file, this.formData)) {

                this.formData.append(this.fieldName + "[]", file);
                
                this.loadImage(file, index);

                this.length += 1;
            }
        });

        this.onChange(); // Run after file selection, sort or delete actions are completed. This can be even if all images select, few selected or notanything selectd.
    }

    imagesValidation(file, formData) {
      
        // Validation rules...

        const extension = file.name.split('.').pop().toLowerCase();

        // Validation rules.

        if (!this.allowSameFile) {

            for (let selectedFile of formData) {
            
                // check the name, size, type

                const checkName = selectedFile.name == file.name;

                const checkSize = selectedFile.size == file.size;

                const checkType = selectedFile.type == file.type;

                if(checkName && checkSize && checkType){
                    // Skip processing : file allready exists.
                    return false;
                }
            }
        }

        if (this.maxImages !== -1) {
            // If the maximum is not set or it's equal to -1:infinite

            if (this.length >= this.maxImages) {
                // Skip processing : Maximum files count reached
                return false;
            }
        }
        
        if(!this.allowedExtensions.includes("*")){
            if (!this.allowedExtensions.includes(extension)) {
                // Skip processing : invalid file extensions
                return false;
            }
        }

        if (this.maxFileSize !== -1) {
            if(file.size / 1024 > this.maxFileSize){
                // Skip processing : maximum file size exceeds
                return false;
            }
        }

        this.onValidationInit(); // Run after other validations are done and. Can be use to set custom validation. Only have to `return true;` to set validate.
        
        return true;
    }

    loadImage(file, index) {
      
        const reader = new FileReader();
        
        reader.onload = function (e) {
        
            const previewContainer = this.createPreviewContainer(e.target.result, index, file);
            
            this.imagePreview.appendChild(previewContainer);
            
            this.imagesOrder.push({ type: this.imagesOrderKeywords.new, key: index });
            
        }.bind(this);
        
        reader.readAsDataURL(file);
    }

    createPreviewContainer(src, index, file = null) {

        const previewContainer = document.createElement("div");
        previewContainer.classList.add(...this.styles.previewContainer);
        
        if (file !== null) {
            previewContainer.imageOrder = { type: this.imagesOrderKeywords.new, key: index };
        } else {
            previewContainer.imageOrder = { type: this.imagesOrderKeywords.old, key: index };
        }

        const previewImage = document.createElement("img");
        previewImage.classList.add(...this.styles.previewImage);
        previewImage.src = src;

        const deleteButton = document.createElement("button");
        deleteButton.classList.add(...this.styles.deleteButton);
        deleteButton.innerHTML = this.deleteButtonHtml;

        if (file !== null) {
            deleteButton.addEventListener("click", () => this.deleteImage(previewContainer, file));
        } else {
            deleteButton.addEventListener("click", () => this.deleteImage(previewContainer));
        }

        previewContainer.appendChild(previewImage);
        previewContainer.appendChild(deleteButton);

        return previewContainer;
    }
    
    deleteImage(previewContainer, file = null) {

        this.imagePreview.removeChild(previewContainer);

        if (file !== null) {

            const index = Object.values(this.formData.getAll(this.fieldName + "[]")).indexOf(file);

            if (index !== -1) {
                
                const files = this.formData.getAll(this.fieldName + "[]");

                // Make sure index is within the valid range
                if (index >= 0 && index < files.length) {

                    files.splice(index, 1);
                }

                this.formData.delete(this.fieldName + "[]");

                for (const file of files) {

                    this.formData.append(this.fieldName + "[]", file);
                }
            }
        
        } else {

            const index = previewContainer.imageOrder.key;

            if (index !== -1) {

                this.existingImages.splice(index, 1);
            }
        }

        const typeToDelete = previewContainer.imageOrder.type;

        const keyToDelete = previewContainer.imageOrder.key;

        const indexToDelete = this.imagesOrder.findIndex(item => item.type === typeToDelete && item.key === keyToDelete);

        if (indexToDelete !== -1) {
            
            this.imagesOrder.splice(indexToDelete, 1);
        }
        
        this.length -= 1;

        this.onDelete(); // Run after other validations are done and. Can be use to set custom validation. Only have to `return true;` to set validate.

        this.onChange(); // Run after file selection, sort or delete actions are completed. This can be even if all images select, few selected or notanything selectd.
    }

    sort() {

        const children = this.imagePreview.children;

        const newImagesOrder = [];

        for (let i = 0; i < children.length; i++) {

            const childElement = children[i];

            newImagesOrder.push(childElement.imageOrder);
        }
        
        this.imagesOrder = newImagesOrder;

        this.onSort(); // Run after images are sorted. use jqury ui for this.

        this.onChange(); // Run after file selection, sort or delete actions are completed. This can be even if all images select, few selected or notanything selectd.
    }
}