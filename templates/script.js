
			jQuery(function ($) {
				$(".sidebar-dropdown > a").click(function() {
				$(".sidebar-submenu").slideUp(200);
				if (
				$(this)
				.parent()
				.hasClass("active")
				) {
				$(".sidebar-dropdown").removeClass("active");
				$(this)
				.parent()
				.removeClass("active");
				} else {
				$(".sidebar-dropdown").removeClass("active");
				$(this)
				.next(".sidebar-submenu")
				.slideDown(200);
				$(this)
				.parent()
				.addClass("active");
				}
				});

				$("#close-sidebar").click(function() {
				$(".page-wrapper").removeClass("toggled");
				});
				$("#show-sidebar").click(function() {
				$(".page-wrapper").addClass("toggled");
				});
			});

			$(document).ready(function(){
    
				$('[data-toggle="popover"]').popover({
					placement : 'top',
					trigger : 'hover'
				});
			});


			//----------------Chart-------------------
			// Load the Visualization API and the corechart package.
			google.charts.load('current', {'packages':['corechart']});

			// Set a callback to run when the Google Visualization API is loaded.
			google.charts.setOnLoadCallback(drawChart);
	  
			// Callback that creates and populates a data table,
			// instantiates the pie chart, passes in the data and
			// draws it.
			function drawChart() {
	  
			  // Create the data table.
			  var data = new google.visualization.DataTable();
			  data.addColumn('string', 'Topping');
			  data.addColumn('number', 'Slices');
			  data.addRows([
				['Mushrooms', 3],
				['Onions', 1],
				['Olives', 1],
				['Zucchini', 1],
				['Pepperoni', 2]
			  ]);
	  
			  // Set chart options
			  var options = {'title':'Totale des projets',
							 'width':700,
							 'height':675};
	  
			  // Instantiate and draw our chart, passing in some options.
			  var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
			  chart.draw(data, options);
			}
	  