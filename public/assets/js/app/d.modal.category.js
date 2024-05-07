/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

/* Prevent errors, If these variables are missing. */
if (typeof packageIsEnabled === 'undefined') {
	var packageIsEnabled = false;
}
var select2Language = languageCode;
if (typeof langLayout !== 'undefined' && typeof langLayout.select2 !== 'undefined') {
	select2Language = langLayout.select2;
}
if (typeof permanentPostsEnabled === 'undefined') {
	var permanentPostsEnabled = 0;
}
if (typeof postTypeId === 'undefined') {
	var postTypeId = 0;
}
if (typeof editLabel === 'undefined') {
	var editLabel = 'Edit';
}

$(document).ready(function () {

	$("#main-category-selection").show();
	$("#post-details-section").hide();

	/* Select a category */
	getCategories(siteUrl, languageCode);
	$(document).on('click', '.cat-link, #selectCats .page-link', function (e) {
		e.preventDefault(); /* prevents submit or reload */
		$("#selectCats").show();
		$("#selectSubCats").hide();
		getCategories(siteUrl, languageCode, this);
	});

	$("#nextStepBtnToPostDetails").click(function () {
		$("#main-category-selection").hide();
		$("#post-details-section").show();
	});

	$(document).on('click', '.cat-link[data-has-children=0]', function (e) {
		/*
		e.preventDefault(); // prevents submit or reload
		$("#main-category-selection").hide();
		$("#post-details-section").show();
		*/
	});

	$("#prevStepBtnToCategories").click(function () {
		$("#main-category-selection").show();
		$("#post-details-section").hide();
	});

	$(document).on("click", ".sub_category_group_bt", function (e) {
		$("#selectCats").hide();
		$("#selectSubCats").show();

		let csrfToken = $('input[name=_token]').val();
		let subCategoryGroupId = $(this).attr('sub_category_group_id');

		/* Get Request URL */
		let url = siteUrl + '/ajax/sub-category-groups/select';

		$("#selectSubCats").html(`<div class="col-xl-12 content-box layout-section" style="border: 0px;padding: 15px;"><div class="row row-featured row-featured-category"><div class="col-12 text-center"><svg aria-hidden="true" role="status" class="mr-3 inline h-4 w-4 animate-spin text-gray-200 dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" /><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2" /></svg>  Loading...</div></div></div>`);
		
		// console.log(`csrfToken : ${csrfToken}\n subCategoryGroupId : ${subCategoryGroupId}`);

		$.post( url, { _token: csrfToken, subCategoryGroupId: subCategoryGroupId })
			.done(function (data) {
			
				//console.dir(data);

				let subCategoryGroups = '';

				if (data === undefined || data.length == 0) {
					subCategoryGroups = `<div class="col-12 text-center">No sub Groups</div>`;
				} else {
					data.forEach(element => {
						subCategoryGroups += `<div class="col-lg-2 col-md-3 col-sm-4 col-6 f-category"><a href="#" class="cat-link" data-id="${element.id}" data-parent-id="${element.parent_id}" data-has-children="${element.children}" data-type="${element.type}"><img src="${element.picture_url}" alt="${element.name}" height="60px"><h6 class="">${element.name}</h6></a></div>`;
					});
				}

				$("#selectSubCats").html(`<div class="col-xl-12 content-box layout-section"><div class="row row-featured row-featured-category">${subCategoryGroups}</div></div>`);

				sessionStorage.setItem("sub_category_group_id", subCategoryGroupId);
		});
	});
	
	/* Show the permanent listings option field */
	showPermanentPostsOption(permanentPostsEnabled, postTypeId);
	$('input[name="post_type_id"]').on('click', function () {
		postTypeId = $(this).val();
		showPermanentPostsOption(permanentPostsEnabled, postTypeId);
	});
	
});

/**
 * Get subcategories buffer and/or Append selected category
 *
 * @param siteUrl
 * @param languageCode
 * @param jsThis
 * @returns {boolean}
 */
function getCategories(siteUrl, languageCode, jsThis = null) {
	let csrfToken = $('input[name=_token]').val();
	
	/* Get Request URL */
	let url = siteUrl + '/ajax/categories/select';
	
	let selectedCatId = $('#categoryId').val();
	let catId;
	
	if (!isDefined(jsThis) || jsThis === null) {
		catId = !isEmpty(selectedCatId) ? selectedCatId : 0;
	} else {
		let thisEl = $(jsThis);
		
		let thisElClass = thisEl.attr('class');
		if (thisElClass == 'page-link') {
			
			url = thisEl.attr('href');
			
			/* Extract the category ID */
			catId = 0;
			if (!isEmpty(url)) {
				let queryString = getQueryParams(url);
				catId = isDefined(queryString.catId) ? queryString.catId : 0;
			}
			
		} else {
			
			/* Get the category ID */
			catId = thisEl.data('id');
			catId = !isEmpty(catId) ? catId : 0;
			
		}
		
		/*
		 * Optimize the category selection
		 * by preventing AJAX request to append the selection
		 */
		let hasChildren = thisEl.data('has-children');
		if (isDefined(hasChildren) && hasChildren == 0) {
			let catName = thisEl.text();
			let catType = thisEl.data('type');
			let parentId = thisEl.data('parent-id');
			
			let linkText = '<i class="far fa-edit"></i> ' + editLabel;
			let outputHtml = catName + '[ <a href="#browseCategories" data-bs-toggle="modal" class="cat-link" data-id="' + parentId + '" >' + linkText + '</a> ]';
			
			return appendSelectedCategory(siteUrl, languageCode, catId, catType, outputHtml);
		}
	}
	
	/* AJAX Call */
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: {
			'_token': csrfToken,
			'selectedCatId': selectedCatId,
			'catId': catId
		},
		beforeSend: function() {
			/*
			let spinner = '<i class="spinner-border"></i>';
			$('#selectCats').addClass('text-center').html(spinner);
			*/
			
			let selectCatsEl = $('#selectCats');
			selectCatsEl.empty().addClass('py-4').busyLoad('hide');
			selectCatsEl.busyLoad('show', {
				text: loadingWd,
				custom: createCustomSpinnerEl(),
				background: '#fff',
				containerItemClass: 'm-5',
			});
		}
	});
	ajax.done(function (xhr) {
		let selectCatsEl = $('#selectCats');
		selectCatsEl.removeClass('py-4').busyLoad('hide');
		
		if (!isDefined(xhr.html) || !isDefined(xhr.hasChildren)) {
			return false;
		}
		
		/* Get & append the category's children */
		if (xhr.hasChildren) {
			selectCatsEl.removeClass('text-center');
			selectCatsEl.html(xhr.html);
		} else {
			/*
			 * Section to append default category field info
			 * or to append selected category during form loading.
			 * Not intervene when the onclick event is fired.
			 */
			if (!isDefined(xhr.category) || !isDefined(xhr.category.id) || !isDefined(xhr.category.type) || !isDefined(xhr.html)) {
				return false;
			}
			
			appendSelectedCategory(siteUrl, languageCode, xhr.category.id, xhr.category.type, xhr.html);
		}
	});
	ajax.fail(function(xhr) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error', false, true);
			
			/* Close the Modal */
			let modalEl = document.querySelector('#browseCategories');
			if (typeof modalEl !== 'undefined' && modalEl !== null) {
				let modalObj = bootstrap.Modal.getInstance(modalEl);
				if (modalObj !== null) {
					modalObj.hide();
				}
			}
		}
	});
}

/**
 * Append the selected category to its field in the form
 *
 * @param siteUrl
 * @param languageCode
 * @param catId
 * @param catType
 * @param outputHtml
 * @returns {boolean}
 */
function appendSelectedCategory(siteUrl, languageCode, catId, catType, outputHtml) {
	if (!isDefined(catId) || !isDefined(catType) || !isDefined(outputHtml)) {
		return false;
	}
	
	try {
		/* Select the category & append it */
		$('#catsContainer').html(outputHtml);
		
		/* Save data in hidden field */
		$('#categoryId').val(catId);
		$('#categoryType').val(catType);
		
		/* Close the Modal */
		let modalEl = document.querySelector('#browseCategories');
		if (isDefined(modalEl) && modalEl !== null) {
			let modalObj = bootstrap.Modal.getInstance(modalEl);
			if (modalObj !== null) {
				modalObj.hide();
			}
		}
		
		/* Apply category's type actions & Get category's custom-fields */
		applyCategoryTypeActions('categoryType', catType, packageIsEnabled);
		getCustomFieldsByCategory(siteUrl, languageCode, catId);
	} catch (e) {
		console.log(e);
	}
	
	return false;
}

/**
 * Get the Custom Fields by Category
 *
 * @param siteUrl
 * @param languageCode
 * @param catId
 * @returns {*}
 */
function getCustomFieldsByCategory(siteUrl, languageCode, catId) {
	/* Check undefined variables */
	if (!isDefined(languageCode) || !isDefined(catId)) {
		return false;
	}
	
	/* Don't make ajax request if any category has selected. */
	if (isEmpty(catId) || catId === 0) {
		return false;
	}
	
	let csrfToken = $('input[name=_token]').val();
	
	let url = siteUrl + '/ajax/categories/' + catId + '/fields';
	
	let dataObj = {
		'_token': csrfToken,
		'languageCode': languageCode,
		'postId': (isDefined(postId)) ? postId : ''
	};
	if (isDefined(errors)) {
		/* console.log(errors); */
		dataObj.errors = errors;
	}
	if (isDefined(oldInput)) {
		/* console.log(oldInput); */
		dataObj.oldInput = oldInput;
	}
	
	let ajax = $.ajax({
		method: 'POST',
		url: url,
		data: dataObj,
		beforeSend: function() {
			let cfEl = $('#cfContainer');
			
			let spinner = '<i class="spinner-border"></i>';
			cfEl.addClass('text-center mb-3').html(spinner);
		}
	});
	ajax.done(function (xhr) {
		let cfEl = $('#cfContainer');
		
		/* Load Custom Fields */
		cfEl.removeClass('text-center mb-3');
		cfEl.html(xhr.customFields);
		
		/* Apply Fields Components */
		initSelect2(cfEl, languageCode);
		cfEl.find('.selecter, .large-data-selecter').select2({
			width: '100%'
		});
	});
	ajax.fail(function(xhr) {
		let message = getJqueryAjaxError(xhr);
		if (message !== null) {
			jsAlert(message, 'error', false);
		}
	});
	
	return catId;
}

/**
 * Apply Category Type actions (for Job offer/search & Services for example)
 *
 * @param categoryTypeFieldId
 * @param categoryTypeValue
 * @param packageIsEnabled
 */
function applyCategoryTypeActions(categoryTypeFieldId, categoryTypeValue, packageIsEnabled) {
	$('#' + categoryTypeFieldId).val(categoryTypeValue);
	
	/* Debug */
	/* console.log(categoryTypeFieldId + ': ' + categoryTypeValue); */
	
	if (categoryTypeValue === 'job-offer') {
		$('#postTypeBloc label[for="post_type_id-1"]').show();
		$('#priceBloc label[for="price"]').html(lang.salary);
		$('#priceBloc').show();
	} else if (categoryTypeValue === 'job-search') {
		$('#postTypeBloc label[for="post_type_id-2"]').hide();
		
		$('#postTypeBloc input[value="1"]').attr('checked', 'checked');
		$('#priceBloc label[for="price"]').html(lang.salary);
		$('#priceBloc').show();
	} else if (categoryTypeValue === 'not-salable') {
		$('#priceBloc').hide();
		
		$('#postTypeBloc label[for="post_type_id-2"]').show();
	} else {
		$('#postTypeBloc label[for="post_type_id-2"]').show();
		$('#priceBloc label[for="price"]').html(lang.price);
		$('#priceBloc').show();
	}
	
	$('#nextStepBtn').html(lang.nextStepBtnLabel.next);
}

function initSelect2(selectElementObj, languageCode) {
	selectElementObj.find('.selecter').select2({
		language: select2Language,
		dropdownAutoWidth: 'true',
		minimumResultsForSearch: Infinity /* Hiding the search box */
	});
	
	selectElementObj.find('.large-data-selecter').select2({
		language: select2Language,
		dropdownAutoWidth: 'true'
	});
}

/**
 * Show the permanent listings option field
 *
 * @param permanentPostsEnabled
 * @param postTypeId
 * @returns {boolean}
 */
function showPermanentPostsOption(permanentPostsEnabled, postTypeId)
{
	if (permanentPostsEnabled == '0') {
		$('#isPermanentBox').empty();
		return false;
	}
	if (permanentPostsEnabled == '1') {
		if (postTypeId == '1') {
			$('#isPermanentBox').removeClass('hide');
		} else {
			$('#isPermanentBox').addClass('hide');
			$('#isPermanent').prop('checked', false);
		}
	}
	if (permanentPostsEnabled == '2') {
		if (postTypeId == '2') {
			$('#isPermanentBox').removeClass('hide');
		} else {
			$('#isPermanentBox').addClass('hide');
			$('#isPermanent').prop('checked', false);
		}
	}
	if (permanentPostsEnabled == '3') {
		var isPermanentField = $('#isPermanent');
		if (isPermanentField.length) {
			if (postTypeId == '2') {
				isPermanentField.val('1');
			} else {
				isPermanentField.val('0');
			}
		}
	}
	if (permanentPostsEnabled == '4') {
		$('#isPermanentBox').removeClass('hide');
	}
}
