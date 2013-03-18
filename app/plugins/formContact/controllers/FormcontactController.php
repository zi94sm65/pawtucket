<?php
/* ----------------------------------------------------------------------
 * includes/formcontactController.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2010 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
   class formcontactController extends ActionController {
 		# -------------------------------------------------------
 		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
 			parent::__construct($po_request, $po_response, $pa_view_paths);
 			
 			$this->opo_plugin_config = Configuration::load($this->request->getAppConfig()->get('application_plugins').'/formContact/conf/formContact.conf');
 			
 			if (!(bool)$this->opo_plugin_config->get('enabled')) { 
                            die(_t('formContact plugin is not enabled'));
                        }
 			
 		}
 		# -------------------------------------------------------
 		public function Index() { 			
 			$this->render('contact_form.php');
 		}
 		# -------------------------------------------------------

 		# -------------------------------------------------------
 		public function thanks() { 			
 			$this->render('send_confirm.php');
 		}
 		# -------------------------------------------------------
 		
  		# -------------------------------------------------------
 		/**
 		 *	send email for all tables other than ca_objects
 		 */
 		public function sendEmail() {
 			$va_errors = array();
 			$vs_controller = "";
			$this->view->setVar("controller", $vs_controller);
		
 			$ps_to_email = $this->opo_plugin_config->get('youremailaddress');
 			$ps_from_email = $this->request->getParameter('from_email', pString);
 			$ps_from_name = $this->request->getParameter('from_name', pString);
 			$ps_subject = $this->request->getParameter('subject', pString);
 			$ps_message = $this->request->getParameter('message', pString);
 			$pn_security = $this->request->getParameter('security', pInteger);
 			$pn_sum = $this->request->getParameter('sum', pInteger);
			
			$o_purifier = new HTMLPurifier();
    		$ps_message = $o_purifier->purify($ps_message);
    		$ps_to_email = $o_purifier->purify($ps_to_email);
    		$ps_from_email = $o_purifier->purify($ps_from_email);
    		$ps_from_name = $o_purifier->purify($ps_from_name);
    		$ps_subject = $o_purifier->purify($ps_subject);
			
			# --- check vars are set and email addresses are valid
			$va_to_email = array();
			$va_to_email_process = array();
			if(!$ps_to_email){
				$va_errors["to_email"] = _t("Please enter a valid email address or multiple addresses separated by commas");
			}else{
				# --- explode on commas to support multiple addresses - then check each one
				$va_to_email_process = explode(",", $ps_to_email);
				foreach($va_to_email_process as $vs_email_to_verify){
					$vs_email_to_verify = trim($vs_email_to_verify);
					if(caCheckEmailAddress($vs_email_to_verify)){
						$va_to_email[$vs_email_to_verify] = "";
					}else{
						$ps_to_email = "";
						$va_errors["to_email"] = _t("Please enter a valid email address or multiple addresses separated by commas");
					}
				}
			}
			if(!$ps_from_email || !caCheckEmailAddress($ps_from_email)){
				$ps_from_email = "";
				$va_errors["from_email"] = _t("Please enter a valid email address");
			}
			if(!$ps_from_name){
				$va_errors["from_name"] = _t("Please enter your name");
			}
			if(!$ps_subject){
				$va_errors["subject"] = _t("Please enter a subject");
			}
			if(!$ps_message && ($this->opo_plugin_config->get('require_message_text'))){
				$va_errors["message"] = _t("Please enter a message");
			}
			if(!$this->request->isLoggedIn()){
				# --- check for security answer if not logged in
				if ((!$pn_security)) {
					$va_errors["security"] = _t("Please answer the security question.");
				}else{
					if($pn_security != $pn_sum){
						$va_errors["security"] = _t("Your answer was incorrect, please try again");
					}
				}
			}
 			
 			$this->view->setVar('errors', $va_errors);

 			if(sizeof($va_errors) == 0){
				# -- generate mail text from template - get both html and text versions
				ob_start();
				require($this->request->getAppConfig()->get('application_plugins')."/formContact/views/mailTemplates/share_email_text.tpl");
				$vs_mail_message_text = ob_get_contents();
				ob_end_clean();
				ob_start();
				require($this->request->getAppConfig()->get('application_plugins')."/formContact/views/mailTemplates/share_email_html.tpl");
				$vs_mail_message_html = ob_get_contents();
				ob_end_clean();
				
				if(caSendmail($va_to_email, array($ps_from_email => $ps_from_name), $ps_subject, $vs_mail_message_text, $vs_mail_message_html, null, null, null)){
 				$this->notification->addNotification(_t("Your email was sent."), "message");
$this->response->setRedirect(caNavUrl($this->request, "formContact", "formcontact","thanks"));
 					return;
 				}else{
 					$this->notification->addNotification(_t("Your email could not be sent"), "message");
 					$va_errors["email"] = 1;
 				}
 			}
 			if(sizeof($va_errors)){
 				# --- there were errors in the form data, so reload form with errors displayed - pass params to preload form
 				$this->view->setVar('to_email', $ps_to_email);
 				$this->view->setVar('from_email', $ps_from_email);
 				$this->view->setVar('from_name', $ps_from_name);
 				$this->view->setVar('subject', $ps_subject);
 				$this->view->setVar('message', $ps_message);
 				
 				$this->notification->addNotification(_t("There were errors in your form"), "message");
 				$this->render('contact_form.php'); 			
 			}
 		} 		
 		# -------------------------------------------------------
		
 
 	}
 ?>
