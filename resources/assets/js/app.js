
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// const app = new Vue({
// 	el: 'body'
// });

$(document).ready(function () {
	$("#cards").gridalicious({
		gutter: 20,
		selector: '.card',
		animate: true
	});

	$(".utcdate").each(function(){
		var messageLocalTime = moment.utc( $(this).attr('data-utc') ).toDate();
		$(this).html( moment(messageLocalTime).format( $(this).attr('data-dateformat') ) );
	});
});