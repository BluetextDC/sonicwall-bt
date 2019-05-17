<!-- start Simple Custom CSS and JS -->
<script>

	$(document).ready(function(){
		$('.img_phishing').hide();
		$('#imgPhishing_1').show();
		$('.P_TEST_Result_Title').hide();	 
	});

	var PhishingTest = new Object;

		PhishingTest.TestItems = {
			"1": {"subject": "Chrome", "answer": "1", "test_answer": "0"},
			"2": {"subject": "Paypal", "answer": "2", "test_answer": "0"},
			"3": {"subject": "One Drive", "answer": "2", "test_answer": "0"},
			"4": {"subject": "TD Bank", "answer": "2", "test_answer": "0"},
			"5": {"subject": "HubSpot", "answer": "1", "test_answer": "0"},
			"6": {"subject": "DHL", "answer": "2", "test_answer": "0"},
			"7": {"subject": "ANZ Bank", "answer": "2", "test_answer": "0"}
		};
		
		PhishingTest.currentQuestion = 1;
		
		PhishingTest.test = function(value){
			//alert(value);
			index = PhishingTest.currentQuestion;
			PhishingTest.TestItems[index].test_answer = value.toString();
			if (PhishingTest.currentQuestion < 7){
				PhishingTest.currentQuestion += 1;
				$('.current_test').html(PhishingTest.currentQuestion);
				$('.img_phishing').hide();
				$('#imgPhishing_' + PhishingTest.currentQuestion).show();
			}else{
				$('#phishing_question').hide();
				$('#phishing_result').show();
				
				$('.P_TEST_Result_Title').show();	
				$('.P_TEST_Title').hide();	 				
				
				var result_html = '';
				var result_score = 0;

				$.each(PhishingTest.TestItems, function (key, val) {
					var your_answer = get_mapping_pishing_val(PhishingTest.TestItems[key].test_answer);
					var correct_answer = get_mapping_pishing_val(PhishingTest.TestItems[key].answer);

					var result_check = '<img class="phishing-result-icon" title="Incorrect" alt="Incorrect" src="/wp-content/uploads/sites/6/2018/11/snwl-incorrect.png">';
					if (your_answer == correct_answer) {
						result_check = '<img class="phishing-result-icon" title="Correct" alt="Correct" src="/wp-content/uploads/sites/6/2018/11/snwl-correct.png">';
						result_score = result_score + 1;
					}
					//var why_show = "<a class='rs_popup' href='#div_answer_popup' onclick=\"select_answer_img('" + key + "')\" title='Why?' target='_blank'>Why?</a>";
					var why_show = "<a href='/wp-content/uploads/sites/6/2018/11/us_phishingiq_2_a" + key + ".png' target='_blank'>Why?</a>";

					var each_question = "<tr><td>" + key + "</td><td>" + PhishingTest.TestItems[key].subject + "</td><td>" + your_answer + "</td><td>" + correct_answer + "</td><td>" + result_check + "</td><td>" + why_show + "</td></tr>";
					result_html = result_html + each_question
				});

				$("#tblQuizResults tbody").append(result_html);
				$('.result_score').html(result_score);
			}
		}
		
	get_mapping_pishing_val = function (value) {
		switch (value) {
			case "0":
				return "No Answer";
				break;
			case "1":
				return "Legitimate";
				break;
			case "2":
				return "Phishing";
				break;
		}

	};
	
	select_answer_img = function (image_id) {
		var image_src = "/wp-content/uploads/sites/6/2018/11/us_phishingiq_2_a" + image_id + ".png";
		$("#img_answer_correct").attr("src", image_src);
	};

</script><!-- end Simple Custom CSS and JS -->
