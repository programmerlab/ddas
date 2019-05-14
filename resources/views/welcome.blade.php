<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DeliverDas</title>

    <!-- ==================Start Css Link===================== -->
    <!-- font awesome icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
    <!-- fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,300,400,500,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <!-- fav icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('assets/welcomeAssets/images/all-img/fav.jpg') }}">
    <!-- plugins css link -->
    <link rel="stylesheet" href="{{ URL::asset('assets/welcomeAssets/css/plugins.css') }}" >
    <!-- app css -->
    <link rel="stylesheet" href="{{ URL::asset('assets/welcomeAssets/css/app.css') }}" >
    <!-- ==================End Css Link===================== -->
</head>

<body>
    <header class="site-header" id="home">
        <nav class="navbar navbar-expand-lg main-menu  fixed-top">
            <div class="container-fluid custom-container">

                <a href="#!" class="navbar-brand"><img src="{{ URL::asset('assets/welcomeAssets/images/logo/logo.png') }}" alt=""></a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
                <span class="navbar-toggler-icon"></span>
            </button>

                <div class="collapse navbar-collapse" id="siteNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active dropdown"><a href="#home" role="button" class="nav-link smooth-scroll dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">HOME</a>
                            
                        </li>
                        <li class="nav-item"><a href="#feature" class="nav-link smooth-scroll">How to use</a></li>
                        <li class="nav-item"><a href="#abouts" class="nav-link smooth-scroll">About Us</a></li>
                        <li class="nav-item"><a href="#screen" class="nav-link smooth-scroll">Screen</a></li>
                        <li class="nav-item"> <a href="#faq" class="nav-link smooth-scroll">Terms Of Use</a></li>
                        <li class="nav-item"><a href="#contacts" class="nav-link smooth-scroll">Become Partner</a></li>
                        <li class="nav-item"><a href="#privacy-policy" class="nav-link smooth-scroll">Privacy Policy</a></li>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink2">
                                <span class="menu-title">blog pages</span>
                                <a class="dropdown-item" href="blog.html">blog</a>
                                <a class="dropdown-item" href="blog-sidebar.html">blog with sidebar</a>
                                <a class="dropdown-item" href="blog-single.html">blog single</a>
                            </div>
                        </li>
                        <li><a href="#" class="btn-mr pill th-primary-outline"> GET THE APP</a></li>
                    </ul>
                </div>

            </div>
        </nav>
    </header>

    <!-- 
    banner area start
 -->
    <section class="bannerarea home-page-1">
        <div class="container">
            <div class="row bn-height align-items-center">
                <div class="col-lg-9">
                    <div class="banner-content">
                        <h2 class="animation" data-animation="fadeInUp" data-animation-delay="0.6s">Store's to your door</h2>
                        <div class="animation" data-animation="fadeInDown" data-animation-delay="0.6s">
                            <p>Download Android & IOS deliverdas application.</p>
                            <a href="#" class="btn-mr  btn-iconprimary">
                      <span class="icon-element">
                        <i class="icofont icofont-brand-apple"></i>
                        App Store
                      </span>
                    </a>
                            <a href="https://play.google.com/store/apps/details?id=com.fooddelivery.customers&hl=en" class="btn-mr btn-iconsecondary">
                      <span class="icon-element">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/gicon.png') }}" alt="">
                        App Store
                      </span>
                    </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mbl-1-img animation" data-animation="zoomIn" data-animation-delay="0.9s"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/mbl1.png') }}" alt=""></div>
    </section>


    <!-- 
    Services area start
-->

    <section class="feature-area" id="feature">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 sec-titile-wrapper text-center">
                    <h2 class="section-title">How to Use</h2>
                    <p>Lets know how to use our application services.</p>
                </div>
                <!-- end section-titile -->
                <div class="col-lg-3 col-sm-6 text-center animation" data-animation="fadeInUp" data-animation-delay="0.1s">
                    <div class="single-feature">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/s1.png') }}" alt="">
                        <h3>Join Us</h3>
                    </div>
                </div>
                <!-- end single feature -->
                <div class="col-lg-3 col-sm-6 text-center animation" data-animation="fadeInUp" data-animation-delay="0.13s">
                    <div class="single-feature">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/s2.png') }}" alt="">
                        <h3>Select Location</h3>
                    </div>
                </div>
                <!-- end single feature -->
                <div class="col-lg-3 col-sm-6 text-center animation" data-animation="fadeInUp" data-animation-delay="0.16s">
                    <div class="single-feature">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/s3.png') }}" alt="">
                        <h3>Order Items</h3>
                    </div>
                </div>
                <!-- end single feature -->
                <div class="col-lg-3 col-sm-6 text-center animation" data-animation="fadeInUp" data-animation-delay="0.19s">
                    <div class="single-feature">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/s4.png') }}" alt="">
                        <h3>Delivery</h3>
                    </div>
                </div>
                <!-- end single feature -->
            </div>
        </div>
    </section>

    <!-- 
 skill start
 -->

    <section class="expericence-andSkills" id="abouts">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-xl-6">
                    <div class="sec-titile-wrapper">
                        <h2 class="section-title">About Us</h2>
                        <p>DeliverDas</p>
                    </div>
                    <!-- end section titile wrapper -->
                    <div class="miniSkilss">
                        <h4>Who we are</h4>
                        <p>DeliverDas was founded in December 2018. Back then users face delay of their orders.<br/>

We are here to reduce that delay and make sure our customers will receive the order instantly from their local stores at reasonable price.<br/>

DeliverDas has a very unique way of order delivering with less delivery fee and we assure you that Once you use DeliverDas , we make you fall in love with the application.</p>
                    </div>
                    <!-- end mini skiklls -->
                    <div class="miniSkilss">
                        <h4>What we do</h4>
                        <p>We are providing an online platform where our customers can place orders from their near by stores and get it deliver Instantly.</p>
                    </div>
                    <!-- end mini skiklls -->
                    <div class="miniSkilss">
                        <h4>How do we do it </h4>
                        <p>We have divided our task in Phases and each phase will have lots of functionality to help our users to make ordering easy and get their orders with lightning fast delivery.</p>
                    </div>
                    <!-- end mini skiklls -->
                </div>
                <div class="col-lg-6 col-xl-4 align-self-lg-center  offset-xl-1 align-self-xl-end animation" data-animation="bounceInUp">
                    <div class="skl-mbl-img">
                        <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/mbl2.png') }}" alt="">
                    </div>
                    <!-- end skl mobile image -->
                </div>
            </div>
        </div>
    </section>


    <!-- 
amazing screen area start
 -->

    <section class="amazing-screen" id="screen">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 sec-titile-wrapper text-center">
                    <h2 class="section-title">Amazing Screens</h2>
                    
                </div>
                <!-- end section-titile -->
                <div class="col-12">
                    <div class="swiper-container s1">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/Screen-one.png') }}" alt=""></div>
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/App-01.png') }}" alt=""></div>
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/App-02.png') }}" alt=""></div>
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/App-3.png') }}" alt=""></div>
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/App-4.png') }}" alt=""></div>
                            <div class="swiper-slide"><img src="{{ URL::asset('assets/welcomeAssets/images/all-img/App-5.png') }}" alt=""></div>


                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination one"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- 
 skill start
 -->

    <section class="expericence-andSkills faqs" id="faq">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="sec-titile-wrapper">
                        <h2 class="section-title">Terms Of Use</h2>
                        <p>These terms and conditions outline the rules and regulations for the use of Deliveredas.com's Website, located at https://www.deliverdas.com.</p>
						<p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Deliverdas.com if you do not agree to take all of the terms and conditions stated on this page. Our Terms and Conditions were created with the help of the Terms And Conditions Generator and the Privacy Policy Template.</p>
						<p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: "Client", "You" and "Your" refers to you, the person log on this website and compliant to the Company’s terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company. "Party", "Parties", or "Us", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client’s needs in respect of provision of the Company’s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.
</p>
                    </div>
                    <!-- end section titile wrapper -->
                    <div id="accordion">
                        <div class="card custombg">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <a class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Cookies
                  </a>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                   We employ the use of cookies. By accessing Deliverdas.com, you agreed to use cookies in agreement with the Deliveredas.com's Privacy Policy.<br/>
								   Most interactive websites use cookies to let us retrieve the user’s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.


                                </div>
                            </div>
                        </div>
                        <div class="card custombg">
                            <div class="card-header" id="headingTwo">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    License

                  </a>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body">
                                    Unless otherwise stated, Deliveredas.com and/or its licensors own the intellectual property rights for all material on Deliverdas.com. All intellectual property rights are reserved. You may access this from Deliverdas.com for your own personal use subjected to restrictions set in these terms and conditions.

                                </div>
                            </div>
                        </div>
                        <div class="card custombg">
                            <div class="card-header" id="headingThree">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    You must not:

                  </a>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body">
                                   Republish material from Deliverdas.com
Sell, rent or sub-license material from Deliverdas.com
Reproduce, duplicate or copy material from Deliverdas.com
Redistribute content from Deliverdas.com
This Agreement shall begin on the date hereof.
<br/>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Deliveredas.com does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Deliveredas.com,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Deliveredas.com shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.
<br/>
Deliveredas.com reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.


                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingFour">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    You warrant and represent that:


                  </a>
                                </h5>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                <div class="card-body">
                                   You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;
The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;
The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy
The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.
You hereby grant Deliveredas.com a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.



                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingFive">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    Hyperlinking to our Content


                  </a>
                                </h5>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                                <div class="card-body">
                                   The following organizations may link to our Website without prior written approval:<br/>
								   Government agencies;<br/>
Search engines;<br/>
News organizations;<br/>
Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and
System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.
These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party’s site.<br/>
We may consider and approve other link requests from the following types of organizations:
<br/>
commonly-known consumer and/or business information sources;
dot.com community sites;
associations or other groups representing charities;
online directory distributors;
internet portals;
accounting, law and consulting firms; and
educational institutions and trade associations.
We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Deliveredas.com; and (d) the link is in the context of general resource information.
<br/>
These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party’s site.
<br/>
If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Deliveredas.com. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.
<br/>
Approved organizations may hyperlink to our Website as follows:
<br/>
By use of our corporate name; or
By use of the uniform resource locator being linked to; or
By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party’s site.
No use of Deliveredas.com's logo or other artwork will be allowed for linking absent a trademark license agreement.




                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingSix">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                    iFrames



                  </a>
                                </h5>
                            </div>
                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                                <div class="card-body">
                                  Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.



                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingSeven">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                   Content Liability

                  </a>
                                </h5>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                                <div class="card-body">
                                 We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingEight">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                   Your Privacy



                  </a>
                                </h5>
                            </div>
                            <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#accordion">
                                <div class="card-body">
                                 Please read Privacy Policy

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingNine">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                   Reservation of Rights





                  </a>
                                </h5>
                            </div>
                            <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#accordion">
                                <div class="card-body">
                                 We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it’s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.


                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingTen">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                   Removal of links from our website                  </a>
                                </h5>
                            </div>
                            <div id="collapseTen" class="collapse" aria-labelledby="headingTen" data-parent="#accordion">
                                <div class="card-body">
                                If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.
<br/>
We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.


                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="headingEleven">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                   Disclaimer                 </a>
                                </h5>
                            </div>
                            <div id="collapseEleven" class="collapse" aria-labelledby="headingEleven" data-parent="#accordion">
                                <div class="card-body">
                                To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:<br/>
								limit or exclude our or your liability for death or personal injury;
limit or exclude our or your liability for fraud or fraudulent misrepresentation;
limit any of our or your liabilities in any way that is not permitted under applicable law; or
exclude any of our or your liabilities that may not be excluded under applicable law.
The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.
<br/>
As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.


                                </div>
                            </div>
                        </div>
						
						
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    

    <section class="get-the-app">
        <div class="app-overlay"></div>
        <!-- end app overlay -->
        <div class="container">
            <div class="row align-items-center get-app-height">
                <div class="col-lg-6 offset-lg-6 col-xl-5 offset-xl-6 sec-titile-wrapper app-titile">
                    <h2 class="section-title">Get Your Free App</h2>
                    <p>Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt.</p>
                    <a href="#" class="btn-mr  btn-iconprimary2">
            <span class="icon-element">
              <i class="icofont icofont-brand-apple"></i>
              App Store
            </span>
          </a>
                    <a href="#" class="btn-mr btn-iconsecondary2">
            <span class="icon-element">
              <img src="{{ URL::asset('assets/welcomeAssets/images/all-img/gicon.png') }}" alt="">
              App Store
            </span>
          </a>
                </div>
                <!-- end section-titile -->
            </div>
        </div>
    </section>

    <!--privacy policy start-->
	
	<section class="expericence-andSkills faqs" id="privacy-policy">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="sec-titile-wrapper">
                        <h2 class="section-title">Privacy Policy</h2>
                        <p>Your privacy is critically important to us.
DeliverDas is located at</p>
<p>
DeliverDas
 <br/>
7350594567
	</p>					<p>It is DeliverDas’s policy to respect your privacy regarding any information we may collect while operating our website. This Privacy Policy applies to https://deliverdas.com (hereinafter, "us", "we", or "https://deliverdas.com"). We respect your privacy and are committed to protecting personally identifiable information you may provide us through the Website. We have adopted this privacy policy ("Privacy Policy") to explain what information may be collected on our Website, how we use this information, and under what circumstances we may disclose the information to third parties. This Privacy Policy applies only to information we collect through the Website and does not apply to our collection of information from other sources.
This Privacy Policy, together with the Terms and conditions posted on our Website, set forth the general rules and policies governing your use of our Website. Depending on your activities when visiting our Website, you may be required to agree to additional terms and conditions.
</p>
                    </div>
                    <!-- end section titile wrapper -->
                    <div id="accordion">
                        <div class="card custombg">
                            <div class="card-header" id="heading12">
                                <h5 class="mb-0">
                                    <a class="btn btn-link" data-toggle="collapse" data-target="#collapse12" aria-expanded="true" aria-controls="collapse12">
                    Website Visitors
                  </a>
                                </h5>
                            </div>

                            <div id="collapse12" class="collapse show" aria-labelledby="heading12" data-parent="#accordion">
                                <div class="card-body">
                                   Like most website operators, DeliverDas collects non-personally-identifying information of the sort that web browsers and servers typically make available, such as the browser type, language preference, referring site, and the date and time of each visitor request. DeliverDas’s purpose in collecting non-personally identifying information is to better understand how DeliverDas’s visitors use its website. From time to time, DeliverDas may release non-personally-identifying information in the aggregate, e.g., by publishing a report on trends in the usage of its website.
DeliverDas also collects potentially personally-identifying information like Internet Protocol (IP) addresses for logged in users and for users leaving comments on https://deliverdas.com blog posts. DeliverDas only discloses logged in user and commenter IP addresses under the same circumstances that it uses and discloses personally-identifying information as described below.

                                </div>
                            </div>
                        </div>
                        <div class="card custombg">
                            <div class="card-header" id="heading13">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
                    Gathering of Personally-Identifying Information

                  </a>
                                </h5>
                            </div>
                            <div id="collapse13" class="collapse" aria-labelledby="heading13" data-parent="#accordion">
                                <div class="card-body">
                                   Certain visitors to DeliverDas’s websites choose to interact with DeliverDas in ways that require DeliverDas to gather personally-identifying information. The amount and type of information that DeliverDas gathers depends on the nature of the interaction. For example, we ask visitors who sign up for a blog at https://deliverdas.com to provide a username, phone number and email address.

                                </div>
                            </div>
                        </div>
                        <div class="card custombg">
                            <div class="card-header" id="heading14">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
                    Security


                  </a>
                                </h5>
                            </div>
                            <div id="collapse14" class="collapse" aria-labelledby="heading14" data-parent="#accordion">
                                <div class="card-body">
                                 The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information, we cannot guarantee its absolute security.


                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading15">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse15" aria-expanded="false" aria-controls="collapse15">
                    Advertisements



                  </a>
                                </h5>
                            </div>
                            <div id="collapse15" class="collapse" aria-labelledby="heading15" data-parent="#accordion">
                                <div class="card-body">
                                   Ads appearing on our website may be delivered to users by advertising partners, who may set cookies. These cookies allow the ad server to recognize your computer each time they send you an online advertisement to compile information about you or others who use your computer. This information allows ad networks to, among other things, deliver targeted advertisements that they believe will be of most interest to you. This Privacy Policy covers the use of cookies by DeliverDas and does not cover the use of cookies by any advertisers.




                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading16">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse16" aria-expanded="false" aria-controls="collapse16">
                    Links To External Sites



                  </a>
                                </h5>
                            </div>
                            <div id="collapse16" class="collapse" aria-labelledby="heading16" data-parent="#accordion">
                                <div class="card-body">
                                   Our Service may contain links to external sites that are not operated by us. If you click on a third party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy and terms and conditions of every site you visit.
We have no control over, and assume no responsibility for the content, privacy policies or practices of any third party sites, products or services.





                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading17">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse17" aria-expanded="false" aria-controls="collapse17">
                    Protection of Certain Personally-Identifying Information



                  </a>
                                </h5>
                            </div>
                            <div id="collapse17" class="collapse" aria-labelledby="heading17" data-parent="#accordion">
                                <div class="card-body">
                                  DeliverDas discloses potentially personally-identifying and personally-identifying information only to those of its employees, contractors and affiliated organizations that (i) need to know that information in order to process it on DeliverDas’s behalf or to provide services available at DeliverDas’s website, and (ii) that have agreed not to disclose it to others. Some of those employees, contractors and affiliated organizations may be located outside of your home country; by using DeliverDas’s website, you consent to the transfer of such information to them. DeliverDas will not rent or sell potentially personally-identifying and personally-identifying information to anyone. Other than to its employees, contractors and affiliated organizations, as described above, DeliverDas discloses potentially personally-identifying and personally-identifying information only in response to a subpoena, court order or other governmental request, or when DeliverDas believes in good faith that disclosure is reasonably necessary to protect the property or rights of DeliverDas, third parties or the public at large.
If you are a registered user of https://deliverdas.com and have supplied your email address and phone number, DeliverDas may occasionally send you an email and message to tell you about new features, solicit your feedback, or just keep you up to date with what’s going on with DeliverDas and our products. We primarily use our blog to communicate this type of information, so we expect to keep this type of email to a minimum. If you send us a request (for example via a support email or via one of our feedback mechanisms), we reserve the right to publish it in order to help us clarify or respond to your request or to help us support other users. DeliverDas takes all measures reasonably necessary to protect against the unauthorized access, use, alteration or destruction of potentially personally-identifying and personally-identifying information.

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading18">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse18" aria-expanded="false" aria-controls="collapse18">
                   Aggregated Statistics


                  </a>
                                </h5>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                                <div class="card-body">
                                 DeliverDas may collect statistics about the behavior of visitors to its website. DeliverDas may display this information publicly or provide it to others. However, DeliverDas does not disclose your personally-identifying information.

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading19">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse19" aria-expanded="false" aria-controls="collapse19">
                   Affiliate Disclosure




                  </a>
                                </h5>
                            </div>
                            <div id="collapse19" class="collapse" aria-labelledby="heading19" data-parent="#accordion">
                                <div class="card-body">
                                This site uses affiliate links and does earn a commission from certain links. This does not affect your purchases or the price you may pay.

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading20">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse20" aria-expanded="false" aria-controls="collapse20">
                   Cookies

                  </a>
                                </h5>
                            </div>
                            <div id="collapse20" class="collapse" aria-labelledby="heading20" data-parent="#accordion">
                                <div class="card-body">
                                 To enrich and perfect your online experience, DeliverDas uses "Cookies", similar technologies and services provided by others to display personalized content, appropriate advertising and store your preferences on your computer.
A cookie is a string of information that a website stores on a visitor’s computer, and that the visitor’s browser provides to the website each time the visitor returns. DeliverDas uses cookies to help DeliverDas identify and track visitors, their usage of https://deliverdas.com, and their website access preferences. DeliverDas visitors who do not wish to have cookies placed on their computers should set their browsers to refuse cookies before using DeliverDas’s websites, with the drawback that certain features of DeliverDas’s websites may not function properly without the aid of cookies.
By continuing to navigate our website without changing your cookie settings, you hereby acknowledge and agree to DeliverDas's use of cookies.

                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading21">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse21" aria-expanded="false" aria-controls="collapse21">
                   E-commerce              </a>
                                </h5>
                            </div>
                            <div id="collapse21" class="collapse" aria-labelledby="heading21" data-parent="#accordion">
                                <div class="card-body">
                               Those who engage in transactions with DeliverDas – by purchasing DeliverDas's services or products, are asked to provide additional information, including as necessary the personal and financial information required to process those transactions. In each case, DeliverDas collects such information only insofar as is necessary or appropriate to fulfil the purpose of the visitor’s interaction with DeliverDas. DeliverDas does not disclose personally-identifying information other than as described below. And visitors can always refuse to supply personally-identifying information, with the caveat that it may prevent them from engaging in certain website-related activities.




                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading22">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse22" aria-expanded="false" aria-controls="collapse22">
                   Business Transfers               </a>
                                </h5>
                            </div>
                            <div id="collapse22" class="collapse" aria-labelledby="heading22" data-parent="#accordion">
                                <div class="card-body">
                               If DeliverDas, or substantially all of its assets, were acquired, or in the unlikely event that DeliverDas goes out of business or enters bankruptcy, user information would be one of the assets that is transferred or acquired by a third party. You acknowledge that such transfers may occur, and that any acquirer of DeliverDas may continue to use your personal information as set forth in this policy.



                                </div>
                            </div>
                        </div>
						
						<div class="card custombg">
                            <div class="card-header" id="heading23">
                                <h5 class="mb-0">
                                    <a class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse23" aria-expanded="false" aria-controls="collapse23">
                   Privacy Policy Changes             </a>
                                </h5>
                            </div>
                            <div id="collapse23" class="collapse" aria-labelledby="heading23" data-parent="#accordion">
                                <div class="card-body">
                               Although most changes are likely to be minor, DeliverDas may change its Privacy Policy from time to time, and in DeliverDas’s sole discretion. DeliverDas encourages visitors to frequently check this page for any changes to its Privacy Policy. Your continued use of this site after any change in this Privacy Policy will constitute your acceptance of such change.



                                </div>
                            </div>
                        </div>
						
						
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    


    <!-- get In tocch -->
    <section class="get_intocuh" id="contacts">
        <div id="googleMap"></div>
        <!-- google map -->
        <div class="container">
            <div class="row">
                <div class="col-12 get-in-box">
                    <div class="row">
                        <h3 class="col-lg-9 offset-lg-1 get-title">Get In Touch</h3>
                        <div class="col-lg-3 offset-lg-1 col-md-4">
                            <div class="single-get-intocuh">

                                <h5><i class="icofont icofont-social-google-map"></i>Address</h5>
                                <address>
                A 1208, Guardian Hillshire,<br>
                Wagholi,Pune.
              </address>
                            </div>
                            <!-- end single get in touch -->
                        </div>
                        <!-- end single get in touch -->
                        <div class="col-lg-4 text-md-center col-md-4">
                            <div class="single-get-intocuh border-LR">
                                <h5><i class="icofont icofont-ui-call"></i>Phone</h5>
                                <address>
                +91-7350594567<br>
                +91-8237272409<br>
                +91-9406815001<br>   
              </address>
                            </div>
                            <!-- end single get in touch -->
                        </div>
                        <!-- end single get in touch -->
                        <div class="col-lg-3 offset-lg-1 col-md-4">
                            <div class="single-get-intocuh">
                                <h5><i class="icofont icofont-email"></i>Email</h5>
                                <address>
               <a href="mailto:infoname@gmail.com">support@deliverdas.com</a><br>
               <a href="mailto:deliverdas@gmail.com">partner@deliverdas.com</a>
                
              </address>
                            </div>
                            <!-- end single get in touch -->
                        </div>
                        <!-- end single get in touch -->
                    </div>
                </div>
                <!-- end get in box -->
                <div class="col-12 get-in-form">
                    <form>
                        <div class="form-row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Name">
                            </div>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" placeholder="mobile">
                            </div>
                            <div class="col-md-6">
                                <input type="email" class="form-control" placeholder="email">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="subject">
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control" placeholder="write your message"></textarea>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn-mr th-primary pill">SEND</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>

	

    <!-- 
  footer start
 -->

    <footer class="site-footer new-footer" style="padding:20px 0px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="single-footer">
                        <a href="#"> <img src="{{ URL::asset('assets/welcomeAssets/images/logo/flogo.png') }}" alt="" class="footer-logo" style="margin-bottom:5px;"></a>
                        
                        <p>&copy;2019 <a href="#">Deliverdas</a> All Rights Reserved</p>
                    </div>
                </div>
				<div class="col-lg-6 col-md-6 col-12">
                    <div class="single-footer">
                        <a href="cancellation-policy.html" class="pull-right">Returns, refund & cancellation</a>
                        
                    </div>
                </div>
                <!-- end single footer -->
                
                <!-- end single footer -->
                
                <!-- end single footer -->
                
                <!-- end single footer -->
            </div>
        </div>
    </footer>




    <!-- map api -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDMjWSDG4QO9RnoYOsLOKITmRLbkg6B5TM"></script>
    <!-- plugin js scripts -->
    <script src="{{ URL::asset('assets/welcomeAssets/js/plugins.js') }}"></script>
    <!-- app js -->
    <script src="{{ URL::asset('assets/welcomeAssets/js/app.js') }}"></script>
    <script>
        var myCenter = new google.maps.LatLng(23.8294748, 90.3845342);

        function initialize() {
            var mapProp = {
                center: myCenter,
                scrollwheel: false,
                zoom: 4,
                zoomControl: true,
                mapTypeControl: true,
                streetViewControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#e9e9e9"
                            },
                            {
                                "lightness": 17
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#f5f5f5"
                            },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.fill",
                        "stylers": [{
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 17
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [{
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 29
                            },
                            {
                                "weight": 0.2
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 18
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#f5f5f5"
                            },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#f5f8fd"
                            },
                            {
                                "lightness": 21
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [{
                                "visibility": "on"
                            },
                            {
                                "color": "#ffffff"
                            },
                            {
                                "lightness": 16
                            }
                        ]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [{
                                "saturation": 36
                            },
                            {
                                "color": "#333333"
                            },
                            {
                                "lightness": 40
                            }
                        ]
                    },
                    {
                        "elementType": "labels.icon",
                        "stylers": [{
                            "visibility": "off"
                        }]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry",
                        "stylers": [{
                                "color": "#f2f2f2"
                            },
                            {
                                "lightness": 19
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [{
                                "color": "#fefefe"
                            },
                            {
                                "lightness": 20
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.stroke",
                        "stylers": [{
                                "color": "#fefefe"
                            },
                            {
                                "lightness": 17
                            },
                            {
                                "weight": 1.2
                            }
                        ]
                    }
                ]
            };

            var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

            var marker = new google.maps.Marker({
                position: myCenter,
                animation: google.maps.Animation.DROP,
                icon: 'assets/welcomeAssets/images/all-img/mapi.gif'
            });
            marker.setMap(map);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</body>
</html>