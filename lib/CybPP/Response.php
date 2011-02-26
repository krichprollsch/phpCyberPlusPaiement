<?php

/*
 * This file is part of the phpCyberPlusPaiement lib.
 *
 * (c) Pierre Tachoire <pierre.tachoire@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * CybPP permet de gérer un paiment en ligne
 * via la technologie de Cyberplus Paiement
 *
 */
class CybPP_Response {

	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible 
	 * (exemple : en cents pour l'Euro).
	 * maxlen 12
	 * @var number
	 */
	protected $amount = null;
	
	/**
	 * Code retour de la demande d'autorisation retournée par la banque émettrice, si
	 * disponible (vide sinon).
	 * len 2
	 * @var string
	 * 00 transaction approuvée ou traitée avec succès
	 * 02 contacter l’émetteur de carte
	 * 03 accepteur invalide
	 * 04 conserver la carte
	 * 05 ne pas honorer
	 * 07 conserver la carte, conditions spéciales
	 * 08 approuver après identification
	 * 12 transaction invalide
	 * 13 montant invalide
	 * 14 numéro de porteur invalide
	 * 30 erreur de format
	 * 31 identifiant de l’organisme acquéreur inconnu
	 * 33 date de validité de la carte dépassée
	 * 34 suspicion de fraude
	 * 41 carte perdue
	 * 43 carte volée 
	 * 51 provision insuffisante ou crédit dépassé
	 * 54 date de validité de la carte dépassée
	 * 56 carte absente du fichier
	 * 57 transaction non permise à ce porteur
	 * 58 transaction interdite au terminal
	 * 59 suspicion de fraude
	 * 60 l’accepteur de carte doit contacter l’acquéreur
	 * 61 montant de retrait hors limite
	 * 63 règles de sécurité non respectées
	 * 68 réponse non parvenue ou reçue trop tard
	 * 90 arrêt momentané du système
	 * 91 émetteur de cartes inaccessible
	 * 96 mauvais fonctionnement du système
	 * 94 transaction dupliquée
	 * 97 échéance de la temporisation de surveillance globale
	 * 98 serveur indisponible routage réseau demandé à nouveau
	 * 99 incident domaine initiateur
	 */
	protected $auth_result = null;
	
	/**
	 * Indique comment a été réalisée la demande d’autorisation. Ce champ peut
	 * prendre les valeurs suivantes :
	 * - FULL : correspond à une autorisation du montant total de la transaction dans
	 *     le cas d’un paiement unitaire avec remise à moins de 6 jours, ou à une
	 *     autorisation du montant du premier paiement dans le cas du paiement en N
	 *     fois, dans le cas d’une remise de ce premier paiement à moins de 6 jours.
	 * - MARK : correspond à une prise d’empreinte de la carte, dans le cas ou le
	 *     paiement est envoyé en banque à plus de 6 jours.
	 * @var string
	 */
	protected $auth_mode = null;
	
	/**
	 * Numéro d'autorisation retourné par le serveur bancaire, si disponible (vide sinon).
	 * len 6
	 * @var int
	 */
	protected $auth_number = null;
	
	/**
	 * Identique à la requête si il a été spécifié dans celle-ci, sinon retourne la valeur par
	 * défaut configurée.
	 * maxlen 3
	 * @var int
	 */
	protected $capture_delay = null;
	
	/**
	 * Type de carte utilisé pour le paiement, si disponible (vide sinon).
	 * maxlen 127
	 * @var string
	 */
	protected $card_brand = null;
	
	/**
	 * Numéro de carte masqué.
	 * maxlen 19
	 * @var string
	 */
	protected $card_number = null;
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature.
	 * @var string
	 */
	protected $ctx_mode = null;
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * len 3
	 * @var number
	 */
	protected $currency = null;
	
	
	/**
	 * Code complémentaire de réponse. Sa signification dépend de la valeur
	 * renseignée dans result.
	 * Lorsque result vaut 30 (erreur de requête), alors extra_result contient le
	 * code numérique du champ qui comporte une erreur de valorisation ou
	 * de format. Cette valeur peut être renseignée à 99 dans le cas d’une
	 * erreur inconnue dans la requête.
	 * Lorsque result vaut 05 (refusée) ou 00 (acceptée), alors extra_result contient le
	 * code numérique du résultat des contrôles risques.
	 * len 2
	 * @var string
	 */
	protected $extra_result = null;
	
	/**
	 * Ce paramètre obligatoire indique le type du paiement :
	 * - SINGLE indique un paiement unitaire.
	 * - MULTI indique un paiement en plusieurs fois. Dans ce cas, le paramètre est
	 *    constitué de la chaîne « MULTI: », suivi par des paires clés/valeurs séparées par
	 *    des « ; ». Les paramètres sont les suivants :
	 *           o « first » indique le montant du premier paiement.
	 *           o « count » indique le nombre de paiements total.
	 *           o « period » indique l’intervalle en nombre de jours entre 2 paiements.
	 *    Exemple :
	 *       currency=978
	 *       amount=10000
	 *       payment_config=MULTI:first=5000;count=3;period=30
	 *    Dans cette configuration :
	 *    - Un premier paiement de 50 euros sera effectué à aujourd’hui
	 *    + « capture_delay » jours.
	 *    - Un deuxième paiement de 25 euros sera effectué à aujourd’hui + 
	 *    + «capture_delay » + 30 jours.
	 *    - Un troisième et dernier paiement de 25 euros sera effectué à aujourd’hui
	 *    + « capture_delay » + 60 jours.
	 *    Remarque : si la date de validité de la carte ne permet pas de réaliser le
	 *    dernier paiement, la demande sera refusée par la plateforme.
	 * @var string
	 */
	protected $payment_config = null;
	
	/**
	 * Paramètre obligatoire permettant à la plateforme de vérifier la validité de la
	 * requête transmise (voir le chapitre suivant).
	 * @var string
	 */
	protected $signature_received = null;
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * len 8
	 * @var number 
	 */
	protected $site_id = null;
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * len 14
	 * @var number
	 */
	protected $trans_date = null;
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * len 6
	 * @var number
	 */
	protected $trans_id = null;
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * len 1
	 * @var number
	 */
	protected $validation_mode = null;
	
	/**
	 *   Si l’autorisation a été réalisée avec succès, indique la garantie du paiement, liée
	 *     à 3D-Secure :
	 *           YES          Le paiement est garanti
	 *           NO          Le paiement n’est pas garanti
	 *           UNKNOWN          Suite à une erreur technique, le paiement ne peut pas être garanti
	 *           Non valorisé     Garantie de paiement non applicable
	 * @var string
	 */
	protected $warranty_result = null;
	
	/**
	 * payment_certificate
	 * Si l’autorisation a été réalisée avec succès, la plateforme de paiement délivre un
	 * certificat de paiement. Pour toute question concernant un paiement réalisé sur la
	 * plateforme, cette information devra être communiquée.
	 * len 40
	 * @var string
	 */
	protected  $payment_certificate = null;
	
	/**
	 * result
	 * Code retour général. Est l'une des valeurs suivantes :
	 * - 00 : Paiement réalisé avec succès.
	 * - 02 : Le commerçant doit contacter la banque du porteur.
	 * - 05 : Paiement refusé.
	 * - 17 : Annulation client.
	 * - 30 : Erreur de format de la requête. A mettre en rapport avec la valorisation
	 *    du champ extra_result.
	 * - 96 : Erreur technique lors du paiement.
	 * len 2
	 * @var string
	 */
	protected $result = null;
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @var string
	 */
	protected $version = null;
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * maxlen 12
	 * @var string
	 */
	protected $order_id = null;
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * maxlen 255
	 * @var string
	 */
	protected $order_info = null;
	protected $order_info2 = null;
	protected $order_info3 = null;
	
	/**
	 * Paramètres optionnels concernant le client, et correspondant respectivement à :
	 *    • cust_name : nom du client
	 *    • cust_title : civilité du client
	 *    • cust_address : adresse du client
	 *    • cust_zip : code postal du client
	 *    • cust_city : vile du client
	 *    • cust_phone : numéro de téléphone du client
	 */
	
	
	/**
	 * maxlen 127
	 * @var string
	 */
	protected $cust_name = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_title = null;
	
	/**
	 * maxlen 255
	 * @var string
	 */
	protected $cust_address = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_zip = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_city = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_phone = null;
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * 
	 * len 2
	 * @var string
	 */
	protected $cust_country = CybPP_Const::DEFAULT_CUST_COUNTRY;
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * len 2
	 * @var string
	 */
	protected $language = null;
	
	/**
	 *     Paramètre facultatif définissant la source du paiement :
	 *     - Paramètre non défini ou valeur vide, indique un paiement de type
	 *        eCommerce. Dans ce cas, la garantie de paiement est calculée
	 *        conformément aux options du commerce concerné.
	 *     - BO indique un paiement effectué depuis le « Back Office » (saisie manuelle),
	 *        dans ce cas il n’y a pas de garantie de paiement.
	 *     - MOTO indique un paiement effectué par un opérateur suite à une
	 *        commande par téléphone ou eMail (Mail Or Telephone Order).
	 *     - CC indique un paiement effectué via un centre d’appel (Call Center).
	 *     - OTHER indique un paiement effectué par toute autre source que celles
	 *        précédemment définies.
	 *     Des informations complémentaires sur l’origine du paiement peuvent être
	 *     définies dans le paramètre user_info.
	 *     NB : L’utilisation de ce paramétrage n’est permise que pour les commerçants
	 *     ayant souscrit une offre adéquate. Merci de contacter votre chargé de
	 *     clientèle bancaire pour plus d’informations.
	 *     
	 * maxlen 5
	 * @var string
	 */
	protected $payment_src = null;
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * maxlen 255
	 * @var string
	 */
	protected $user_info = null;
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * maxlen 255
	 * @var string
	 */
	protected $theme_config = null;
	
	protected $hash = null;
	
	/**
	 * Paramètre obligatoire permettant à la plateforme de vérifier la validité de la
	 * requête transmise (voir le chapitre suivant).
	 * @var string
	 */
	protected $signature_processed = null;
	
	/**
	 * certificat du site
	 * @param string $value
	 * @return void
	 */
	public function setCertificat(  $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) != 16 ) {
			throw new CybPP_Exception( 'Certificat has bad length', -2 );	
		}
		
		$this->certificat = $value;
	}
	
	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible
	 * (exemple : en cents pour l'Euro).
	 * @param int $value
	 * @return void
	 */
	public function setAmount( $value ) {
		
		$value = intval( $value );
		
		if( strlen( strval( $value )) > 12 ) {
			throw new CybPP_Exception( 'Amount is to long', 9 );	
		}
		
		if( $value == null ) {
			throw new CybPP_Exception( 'Amount is mandatory', 9 );
		}
		
		$this->amount = $value;
	}
	
	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible
	 * (exemple : en cents pour l'Euro).
	 * @return int
	 */
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 * Code retour de la demande d'autorisation retournée par la banque émettrice, si
	 * disponible (vide sinon).
	 * len 2
	 * @var string
	 * 00 transaction approuvée ou traitée avec succès
	 * 02 contacter l’émetteur de carte
	 * 03 accepteur invalide
	 * 04 conserver la carte
	 * 05 ne pas honorer
	 * 07 conserver la carte, conditions spéciales
	 * 08 approuver après identification
	 * 12 transaction invalide
	 * 13 montant invalide
	 * 14 numéro de porteur invalide
	 * 30 erreur de format
	 * 31 identifiant de l’organisme acquéreur inconnu
	 * 33 date de validité de la carte dépassée
	 * 34 suspicion de fraude
	 * 41 carte perdue
	 * 43 carte volée 
	 * 51 provision insuffisante ou crédit dépassé
	 * 54 date de validité de la carte dépassée
	 * 56 carte absente du fichier
	 * 57 transaction non permise à ce porteur
	 * 58 transaction interdite au terminal
	 * 59 suspicion de fraude
	 * 60 l’accepteur de carte doit contacter l’acquéreur
	 * 61 montant de retrait hors limite
	 * 63 règles de sécurité non respectées
	 * 68 réponse non parvenue ou reçue trop tard
	 * 90 arrêt momentané du système
	 * 91 émetteur de cartes inaccessible
	 * 96 mauvais fonctionnement du système
	 * 94 transaction dupliquée
	 * 97 échéance de la temporisation de surveillance globale
	 * 98 serveur indisponible routage réseau demandé à nouveau
	 * 99 incident domaine initiateur
	 * @param string $value
	 * @return void
	 */
	public function setAuthResult( $value ) {
		
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) != 2 ) {
			throw new CybPP_Exception( 'Auth Result has bad length' );	
		}
		
		$this->auth_result = $value;
	}
	
	/**
	 * Code retour de la demande d'autorisation retournée par la banque émettrice, si
	 * disponible (vide sinon).
	 * len 2
	 * @var string
	 * 00 transaction approuvée ou traitée avec succès
	 * 02 contacter l’émetteur de carte
	 * 03 accepteur invalide
	 * 04 conserver la carte
	 * 05 ne pas honorer
	 * 07 conserver la carte, conditions spéciales
	 * 08 approuver après identification
	 * 12 transaction invalide
	 * 13 montant invalide
	 * 14 numéro de porteur invalide
	 * 30 erreur de format
	 * 31 identifiant de l’organisme acquéreur inconnu
	 * 33 date de validité de la carte dépassée
	 * 34 suspicion de fraude
	 * 41 carte perdue
	 * 43 carte volée 
	 * 51 provision insuffisante ou crédit dépassé
	 * 54 date de validité de la carte dépassée
	 * 56 carte absente du fichier
	 * 57 transaction non permise à ce porteur
	 * 58 transaction interdite au terminal
	 * 59 suspicion de fraude
	 * 60 l’accepteur de carte doit contacter l’acquéreur
	 * 61 montant de retrait hors limite
	 * 63 règles de sécurité non respectées
	 * 68 réponse non parvenue ou reçue trop tard
	 * 90 arrêt momentané du système
	 * 91 émetteur de cartes inaccessible
	 * 96 mauvais fonctionnement du système
	 * 94 transaction dupliquée
	 * 97 échéance de la temporisation de surveillance globale
	 * 98 serveur indisponible routage réseau demandé à nouveau
	 * 99 incident domaine initiateur
	 * @return string
	 */
	public function getAuthResult() {
		return $this->auth_result;
	}
	
	/**
	 * Indique comment a été réalisée la demande d’autorisation. Ce champ peut
	 * prendre les valeurs suivantes :
	 * - FULL : correspond à une autorisation du montant total de la transaction dans
	 *     le cas d’un paiement unitaire avec remise à moins de 6 jours, ou à une
	 *     autorisation du montant du premier paiement dans le cas du paiement en N
	 *     fois, dans le cas d’une remise de ce premier paiement à moins de 6 jours.
	 * - MARK : correspond à une prise d’empreinte de la carte, dans le cas ou le
	 *     paiement est envoyé en banque à plus de 6 jours.
	 * @param string
	 * @return void
	 */
	public function setAuthMode( $value ) {
		
		$value = strval( $value );
		switch( $value ) {
			case null :
				$this->auth_mode = null;
				break;
			case CybPP_Const::AUTH_MODE_FULL :
				$this->auth_mode = CybPP_Const::AUTH_MODE_FULL;
				break;
			case CybPP_Const::AUTH_MODE_MARK :
				$this->auth_mode = CybPP_Const::AUTH_MODE_MARK;
				break;
			default :
				throw new CybPP_Exception( 'Auth Mode has bad value' );
				break;
		}
	}
	
	/**
	 * Indique comment a été réalisée la demande d’autorisation. Ce champ peut
	 * prendre les valeurs suivantes :
	 * - FULL : correspond à une autorisation du montant total de la transaction dans
	 *     le cas d’un paiement unitaire avec remise à moins de 6 jours, ou à une
	 *     autorisation du montant du premier paiement dans le cas du paiement en N
	 *     fois, dans le cas d’une remise de ce premier paiement à moins de 6 jours.
	 * - MARK : correspond à une prise d’empreinte de la carte, dans le cas ou le
	 *     paiement est envoyé en banque à plus de 6 jours.
	 * @return string
	 */
	public function getAuthMode() {
		return $this->auth_mode;
	}
	

	/**
	 * Numéro d'autorisation retourné par le serveur bancaire, si disponible (vide sinon).
	 * len 6
	 * @param string $value
	 * @return void
	 */
	public function setAuthNumber( $value ) {
		
		$value = strval( $value );
		$this->auth_number = $value;
	}
	

	/**
	 * Numéro d'autorisation retourné par le serveur bancaire, si disponible (vide sinon).
	 * len 6
	 * @return string
	 */
	public function getAuthNumber() {
		return $this->auth_number;
	}
	
	/**
	 * Paramètre obligatoire indiquant le délai en nombre de jours avant remise en
	 * banque. Si ce paramètre est vide (il doit néanmoins être transmis), alors la valeur
	 * par défaut sera utilisée. Cette dernière est paramétrable dans l’outil de gestion
	 * de caisse Cyberplus Paiement par toutes les personnes dûment habilitées.
	 * @param int $value
	 * @return void
	 */
	public function setCaptureDelay( $value ) {
		
		$value = $value;
		
		if( strlen( strval( $value )) > 3 ) {
			throw new CybPP_Exception( 'Capture Delay is to long', 6 );	
		}
		
		$this->capture_delay = $value;
	}
	
	/**
	 * Paramètre obligatoire indiquant le délai en nombre de jours avant remise en
	 * banque. Si ce paramètre est vide (il doit néanmoins être transmis), alors la valeur
	 * par défaut sera utilisée. Cette dernière est paramétrable dans l’outil de gestion
	 * de caisse Cyberplus Paiement par toutes les personnes dûment habilitées.
	 * @return int
	 */
	public function getCaptureDelay() {
		return $this->capture_delay;	
	}
	
	/**
	 * Type de carte utilisé pour le paiement, si disponible (vide sinon).
	 * maxlen 127
	 * @param string $value
	 * @return void
	 */
	public function setCardBrand( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Card Brand is too long' );	
		}
		
		$this->card_brand = $value;
	}
	
	/**
	 * Type de carte utilisé pour le paiement, si disponible (vide sinon).
	 * maxlen 127
	 * @return string
	 */
	public function getCardBrand() {
		return $this->card_brand;
	}
	
	/**
	 * Numéro de carte masqué.
	 * @param string $value
	 * @return void
	 */
	public function setCardNumber( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) > 19 ) {
			throw new CybPP_Exception( 'Card Brand is too long' );	
		}
		
		$this->card_number = $value;
	}
	
	/**
	 * Numéro de carte masqué.
	 * @return string
	 */
	public function getCardNumber() {
		return $this->card_number;
	}
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature. 
	 * @param string $value
	 * @return void
	 */
	public function setCtxMode( $value ) {
		switch( $value ) {
			case CybPP_Const::CTX_MODE_TEST :
				$this->ctx_mode = CybPP_Const::CTX_MODE_TEST;
				break;
			case CybPP_Const::CTX_MODE_PRODUCTION :
				$this->ctx_mode = CybPP_Const::CTX_MODE_PRODUCTION;
				break;
			default :
				throw new CybPP_Exception( 'Bad Ctx Mode', 11 );
		}
	}
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature.
	 * @return string
	 */
	public function getCtxMode() {
		return $this->ctx_mode;
	}
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * @param int $value
	 * @return void
	 */
	public function setCurrency( $value ) {
		$value = intval( $value );
		
		if( strlen( strval( $value ) ) != 3 ) {
			throw new CybPP_Exception( 'Currency has a bad length', 10 );	
		}
		
		if( $value == null ) {
			throw new CybPP_Exception( 'Currency Delay is mandatory', 10 );
		}
		
		$this->currency = $value;
	}
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * @return int
	 */
	public function getCurrency() {
		return $this->currency;
	}
	
	/**
	 * Code complémentaire de réponse. Sa signification dépend de la valeur
	 * renseignée dans result.
	 * Lorsque result vaut 30 (erreur de requête), alors extra_result contient le
	 * code numérique du champ qui comporte une erreur de valorisation ou
	 * de format. Cette valeur peut être renseignée à 99 dans le cas d’une
	 * erreur inconnue dans la requête.
	 * Lorsque result vaut 05 (refusée) ou 00 (acceptée), alors extra_result contient le
	 * code numérique du résultat des contrôles risques.
	 * len 2
	 * @param string $value
	 * @return void
	 */
	public function setExtraResult( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) > 2 ) {
			throw new CybPP_Exception( 'Extra Result is too long' );	
		}
		
		$this->extra_result = $value;
	}
	
	/**
	 * Code complémentaire de réponse. Sa signification dépend de la valeur
	 * renseignée dans result.
	 * Lorsque result vaut 30 (erreur de requête), alors extra_result contient le
	 * code numérique du champ qui comporte une erreur de valorisation ou
	 * de format. Cette valeur peut être renseignée à 99 dans le cas d’une
	 * erreur inconnue dans la requête.
	 * Lorsque result vaut 05 (refusée) ou 00 (acceptée), alors extra_result contient le
	 * code numérique du résultat des contrôles risques.
	 * len 2
	 * @return string
	 */
	public function getExtraResult() {
		return $this->extra_result;
	}
	
	/**
	 * Ce paramètre obligatoire indique le type du paiement :
	 * - SINGLE indique un paiement unitaire.
	 * - MULTI indique un paiement en plusieurs fois. Dans ce cas, le paramètre est
	 *    constitué de la chaîne « MULTI: », suivi par des paires clés/valeurs séparées par
	 *    des « ; ». Les paramètres sont les suivants :
	 *           o « first » indique le montant du premier paiement.
	 *           o « count » indique le nombre de paiements total.
	 *           o « period » indique l’intervalle en nombre de jours entre 2 paiements.
	 *    Exemple :
	 *       currency=978
	 *       amount=10000
	 *       payment_config=MULTI:first=5000;count=3;period=30
	 *    Dans cette configuration :
	 *    - Un premier paiement de 50 euros sera effectué à aujourd’hui
	 *    + « capture_delay » jours.
	 *    - Un deuxième paiement de 25 euros sera effectué à aujourd’hui + 
	 *    + «capture_delay » + 30 jours.
	 *    - Un troisième et dernier paiement de 25 euros sera effectué à aujourd’hui
	 *    + « capture_delay » + 60 jours.
	 *    Remarque : si la date de validité de la carte ne permet pas de réaliser le
	 *    dernier paiement, la demande sera refusée par la plateforme.
	 *    
	 *    EDIT 05/05/2010 : payment config peut être a null dans le cas d'une validation
	 *    via le back office
	 *    
	 * @param string $value
	 * @return void
	 */
	public function setPaymentConfig( $value ) {
		$value = strval( $value );
		
		switch( $value ) {
			case CybPP_Const::PAYMENT_CONFIG_SINGLE :
				$this->payment_config = $value;
				break;
			case CybPP_Const::PAYMENT_CONFIG_NULL :
				$this->payment_config = $value;
				break;
			case CybPP_Const::PAYMENT_CONFIG_MULTI :
				throw new CybPP_Exception( 'Payment Config MULTI not yet supported', 7 );
				break;
			default :
				throw new CybPP_Exception( 'Payment Config has a bad value', 7 );
				break;
		}
	}
	
	/**
	 * Ce paramètre obligatoire indique le type du paiement :
	 * - SINGLE indique un paiement unitaire.
	 * - MULTI indique un paiement en plusieurs fois. Dans ce cas, le paramètre est
	 *    constitué de la chaîne « MULTI: », suivi par des paires clés/valeurs séparées par
	 *    des « ; ». Les paramètres sont les suivants :
	 *           o « first » indique le montant du premier paiement.
	 *           o « count » indique le nombre de paiements total.
	 *           o « period » indique l’intervalle en nombre de jours entre 2 paiements.
	 *    Exemple :
	 *       currency=978
	 *       amount=10000
	 *       payment_config=MULTI:first=5000;count=3;period=30
	 *    Dans cette configuration :
	 *    - Un premier paiement de 50 euros sera effectué à aujourd’hui
	 *    + « capture_delay » jours.
	 *    - Un deuxième paiement de 25 euros sera effectué à aujourd’hui + 
	 *    + «capture_delay » + 30 jours.
	 *    - Un troisième et dernier paiement de 25 euros sera effectué à aujourd’hui
	 *    + « capture_delay » + 60 jours.
	 *    Remarque : si la date de validité de la carte ne permet pas de réaliser le
	 *    dernier paiement, la demande sera refusée par la plateforme. 
	 *    
	 *    EDIT 05/05/2010 : payment config peut être a null dans le cas d'une validation
	 *    via le back office
	 *    
	 * @return string
	 */
	public function getPaymentConfig() {
		return $this->payment_config;
	}
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * @param int $value
	 * @return void
	 */
	public function setSiteId( $value ) {
		$value = intval( $value );
		
		if( strlen( strval( $value ) ) != 8 ) {
			throw new CybPP_Exception( 'Site id as bad length', 2 );	
		}
		
		$this->site_id = $value;
	}
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * @return int
	 */
	public function getSiteId() {
		return $this->site_id;
	}
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * @param string $value
	 * @return void
	 */
	public function setTransDate( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) != 14 ) {
			throw new CybPP_Exception( 'Trans Date as bad length', 4 );	
		}
		
		$this->trans_date = $value;
	}
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * @return string
	 */
	public function getTransDate() {	
		return $this->trans_date;
	}
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * @param string $value
	 * @return void
	 */
	public function setTransId( $value ){
		$value = strval( $value );
		
		if( strlen( $value ) != 6 ) {
			throw new CybPP_Exception( 'Trans Id as bad length', 3 );	
		}
		
		$this->trans_id = $value;
	}
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * @return string
	 */
	public function getTransId() {	
		return $this->trans_id;
	}
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * @param string $value
	 * @return void
	 */
	public function setValidationMode( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) != 1 ) {
			throw new CybPP_Exception( 'Validation Mode as bad length');	
		}
		
		$this->validation_mode = $value;
	}
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * @return string
	 */
	public function getValidationMode() {	
		return $this->validation_mode;
	}
	
	/**
	 *   Si l’autorisation a été réalisée avec succès, indique la garantie du paiement, liée
	 *     à 3D-Secure :
	 *           YES          Le paiement est garanti
	 *           NO          Le paiement n’est pas garanti
	 *           UNKNOWN          Suite à une erreur technique, le paiement ne peut pas être garanti
	 *           Non valorisé     Garantie de paiement non applicable
	 * @param string $value
	 * @return void
	 */
	public function setWarrantyResult( $value ) {
		$value = strval( $value );
		
		switch( $value ) {
			case CybPP_Const::WARRANTY_RESULT_NAN :
			case CybPP_Const::WARRANTY_RESULT_NO :
			case CybPP_Const::WARRANTY_RESULT_UNKNOWN :
			case CybPP_Const::WARRANTY_RESULT_YES :
				$this->warranty_result = $value;
				break;
			default :
				throw new CybPP_Exception( 'Warranty result as bad value');	
				break;		
		}
		
	}
	
	/**
	 *   Si l’autorisation a été réalisée avec succès, indique la garantie du paiement, liée
	 *     à 3D-Secure :
	 *           YES          Le paiement est garanti
	 *           NO          Le paiement n’est pas garanti
	 *           UNKNOWN          Suite à une erreur technique, le paiement ne peut pas être garanti
	 *           Non valorisé     Garantie de paiement non applicable
	 * @return string
	 */
	public function getWarrantyResult() {	
		return $this->warranty_result;
	}
	
	/**
	 * payment_certificate
	 * Si l’autorisation a été réalisée avec succès, la plateforme de paiement délivre un
	 * certificat de paiement. Pour toute question concernant un paiement réalisé sur la
	 * plateforme, cette information devra être communiquée.
	 * len 40
	 * @param string $value
	 * @return void
	 */
	public function setPaymentCertificate( $value ){
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) != 40 ) {
			throw new CybPP_Exception( 'payment certificate as bad length' );	
		}
		
		$this->payment_certificate = $value;
	}
	
	/**
	 * payment_certificate
	 * Si l’autorisation a été réalisée avec succès, la plateforme de paiement délivre un
	 * certificat de paiement. Pour toute question concernant un paiement réalisé sur la
	 * plateforme, cette information devra être communiquée.
	 * len 40
	 * @return string
	 */
	public function getPaymentCertificate() {	
		return $this->payment_certificate;
	}
	
	/**
	 * result
	 * Code retour général. Est l'une des valeurs suivantes :
	 * - 00 : Paiement réalisé avec succès.
	 * - 02 : Le commerçant doit contacter la banque du porteur.
	 * - 05 : Paiement refusé.
	 * - 17 : Annulation client.
	 * - 30 : Erreur de format de la requête. A mettre en rapport avec la valorisation
	 *    du champ extra_result.
	 * - 96 : Erreur technique lors du paiement.
	 * len 2
	 * @param string $value
	 * @return void
	 */
	public function setResult( $value ){
		$value = strval( $value );
		
		if( strlen( $value ) != 2 ) {
			throw new CybPP_Exception( 'Result as bad length' );	
		}
		
		$this->result = $value;
	}
	
	/**
	 * result
	 * Code retour général. Est l'une des valeurs suivantes :
	 * - 00 : Paiement réalisé avec succès.
	 * - 02 : Le commerçant doit contacter la banque du porteur.
	 * - 05 : Paiement refusé.
	 * - 17 : Annulation client.
	 * - 30 : Erreur de format de la requête. A mettre en rapport avec la valorisation
	 *    du champ extra_result.
	 * - 96 : Erreur technique lors du paiement.
	 * len 2
	 * @return string
	 */
	public function getResult() {	
		return $this->result;
	}
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @param string $value
	 * @return void
	 */
	public function setVersion( $value ) {
		switch( $value ) {
			case CybPP_Const::VERSION_V1 :
				$this->version = CybPP_Const::VERSION_V1;
				break;
			default :
				throw new CybPP_Exception( 'Version as bad value', 1 );
				break;
		}
	}
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * @param string $value
	 * @return void
	 */
	public function setOrderId( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 12 ) {
			throw new CybPP_Exception( 'order Id is too long', 13 );	
		}
		
		$this->order_id = $value;
	}
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * @return string
	 */
	public function getOrderId() {
		return $this->order_id;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return string
	 */
	public function getOrderInfo() {
		return $this->order_info;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return string
	 */
	public function getOrderInfo2() {
		return $this->order_info2;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return void
	 */
	public function getOrderInfo3() {
		return $this->order_info3;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @param string
	 * @return void
	 */
	public function setOrderInfo( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'order info is too long', 13 );	
		}
		
		$this->order_info = $value;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @param string
	 * @return void
	 */
	public function setOrderInfo2( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'order info is too long', 13 );	
		}
		
		$this->order_info2 = $value;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @param string
	 * @return void
	 */
	public function setOrderInfo3( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'order info is too long', 13 );	
		}
		
		$this->order_info3 = $value;
	}
	
	/**
	 * cust_address : adresse du client
	 * @param string $value
	 * @return void
	 */
	public function setCustAddress( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'Cust Adress is to long', 19 );	
		}
		
		$this->cust_address = $value;
	}
	
	/**
	 * cust_address : adresse du client
	 * @return string
	 */
	public function getCustAddress() {
		return $this->cust_address;
	}
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * @param string $value
	 * @return void
	 */
	public function setCustCountry( $value ) {
		$value = intval( $value );
		
		if( $value != null && strlen( strval( $value ) ) != 2 ) {
			throw new CybPP_Exception( 'Cust Country has a bad length', 22 );	
		}
		
		$this->cust_country = $value;
	}
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * @return string
	 */
	public function getCustCountry() {
		return $this->cust_country;
	}
	
	/**
	 * Adresse e-mail du client, nécessaire pour lui envoyer un mail récapitulatif de la
	 * transaction. Paramètre optionnel.
	 * @param string $value
	 * @return void
	 */
	public function setCustEmail( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Cust Email is to long', 15 );	
		}
		
		$this->cust_email = $value;
	}
	
	/**
	 * Adresse e-mail du client, nécessaire pour lui envoyer un mail récapitulatif de la
	 * transaction. Paramètre optionnel.
	 * @return string
	 */
	public function getCustEmail() {
		return $this->cust_email;
	}
	
	/**
	 * Paramètre facultatif correspondant à un identifiant client pour le marchand. 
	 * @param string $value
	 * @return void
	 */
	public function setCustId( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Id is to long', 16 );	
		}
		
		$this->cust_id = $value;
	}
	
	/**
	 * Paramètre facultatif correspondant à un identifiant client pour le marchand. 
	 * @return string
	 */
	public function getCustId() {
		return $this->cust_id;
	}
	
	/**
	 * cust_name : nom du client
	 * @param string $value
	 * @return void
	 */
	public function setCustName( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Cust Name is to long', 18 );	
		}
		
		$this->cust_name = $value;
	}
	
	/**
	 * cust_name : nom du client
	 * @return string
	 */
	public function getCustName() {
		return $this->cust_name;
	}
	
	/**
	 * cust_phone : numéro de téléphone du client
	 * @param string $value
	 * @return void
	 */
	public function setCustPhone( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Phone is to long', 23 );	
		}
		
		$this->cust_phone = $value;
	}
	
	/**
	 * cust_phone : numéro de téléphone du client
	 * @return string
	 */
	public function getCustPhone() {
		return $this->cust_phone;
	}
	
	/**
	 * cust_title : civilité du client
	 * @param string $value
	 * @return void
	 */
	public function setCustTitle( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Title is to long', 17 );	
		}
		
		$this->cust_title = $value;
	}
	
	
	/**
	 * cust_title : civilité du client
	 * @return string
	 */
	public function getCustTitle() {
		return $this->cust_title;
	}
	
	/**
	 * cust_city : ville du client
	 * @param string $value
	 * @return void
	 */
	public function setCustCity( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust City is to long', 21 );	
		}
		
		$this->cust_city = $value;
	}
	
	/**
	 * cust_city : ville du client
	 * @return string
	 */
	public function getCustCity() {
		return $this->cust_city;
	}
	
	/**
	 * cust_zip : code postal du client
	 * @param string $value
	 * @return void
	 */
	public function setCustZip( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Zip is to long', 20 );	
		}
		
		$this->cust_zip = $value;
	}
	
	/**
	 * cust_zip : code postal du client
	 * @return string
	 */
	public function getCustZip() {
		return $this->cust_zip;
	}
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * @param string $value
	 * @return void
	 */
	public function setLanguage( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) != 2 ) {
			throw new CybPP_Exception( 'Language has bad lenght', 12 );	
		}
		
		$this->language = $value;
	}
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * @return void
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 *     Paramètre facultatif définissant la source du paiement :
	 *     - Paramètre non défini ou valeur vide, indique un paiement de type
	 *        eCommerce. Dans ce cas, la garantie de paiement est calculée
	 *        conformément aux options du commerce concerné.
	 *     - BO indique un paiement effectué depuis le « Back Office » (saisie manuelle),
	 *        dans ce cas il n’y a pas de garantie de paiement.
	 *     - MOTO indique un paiement effectué par un opérateur suite à une
	 *        commande par téléphone ou eMail (Mail Or Telephone Order).
	 *     - CC indique un paiement effectué via un centre d’appel (Call Center).
	 *     - OTHER indique un paiement effectué par toute autre source que celles
	 *        précédemment définies.
	 *     Des informations complémentaires sur l’origine du paiement peuvent être
	 *     définies dans le paramètre user_info.
	 *     NB : L’utilisation de ce paramétrage n’est permise que pour les commerçants
	 *     ayant souscrit une offre adéquate. Merci de contacter votre chargé de
	 *     clientèle bancaire pour plus d’informations.
	 *     
	 * maxlen 5
	 * @param string $value
	 * @return void
	 */
	public function setPaymentSrc( $value ) {
		$value = strval( $value );
		
		switch( $value ) {
			case CybPP_Const::PAYMENT_SRC_BO :
			case CybPP_Const::PAYMENT_SRC_CC :
			case CybPP_Const::PAYMENT_SRC_ECOMMERCE :
			case CybPP_Const::PAYMENT_SRC_MOTO :
			case CybPP_Const::PAYMENT_SRC_OTHER :
				$this->payment_src = $value;
			default :
				throw new CybPP_Exception( 'Payment Src has bad value', 60 );
		}
	}
	
	/**
	 *     Paramètre facultatif définissant la source du paiement :
	 *     - Paramètre non défini ou valeur vide, indique un paiement de type
	 *        eCommerce. Dans ce cas, la garantie de paiement est calculée
	 *        conformément aux options du commerce concerné.
	 *     - BO indique un paiement effectué depuis le « Back Office » (saisie manuelle),
	 *        dans ce cas il n’y a pas de garantie de paiement.
	 *     - MOTO indique un paiement effectué par un opérateur suite à une
	 *        commande par téléphone ou eMail (Mail Or Telephone Order).
	 *     - CC indique un paiement effectué via un centre d’appel (Call Center).
	 *     - OTHER indique un paiement effectué par toute autre source que celles
	 *        précédemment définies.
	 *     Des informations complémentaires sur l’origine du paiement peuvent être
	 *     définies dans le paramètre user_info.
	 *     NB : L’utilisation de ce paramétrage n’est permise que pour les commerçants
	 *     ayant souscrit une offre adéquate. Merci de contacter votre chargé de
	 *     clientèle bancaire pour plus d’informations.
	 *     
	 * maxlen 5
	 * @return string
	 */
	public function getPaymentSrc() {
		return $this->payment_src;
	}
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * @param string $value
	 * @return void
	 */
	public function setUserInfo( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'User Info is too long', 61 );	
		}
		
		$this->user_info = $value;
	}
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * @return string
	 */
	public function getUserInfo() {
		return $this->user_info;
	}
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * @param string $value
	 * @return void
	 */
	public function setThemeConfig( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'Theme Config is too long', 32 );	
		}
		
		$this->theme_config = $value;
	}
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * @return string
	 */
	public function getThemeConfig() {
		return $this->theme_config;
	}
	
	public function getCertificat() {
		return $this->certificat;
	}
	
	public function setHash( $value ) {
		$this->hash = $value;
	}
	
	public function getHash() {
		return $this->hash;
	}
	
	/**
	 * permet de calculer la signature
	 * @return void
	 */
	public function processSignature() {
		
		$signature = null;
		$signature .= $this->getVersion();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getSiteId();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCtxMode();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getTransId();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getTransDate();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getValidationMode();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCaptureDelay();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getPaymentConfig();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCardBrand();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCardNumber();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getAmount();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCurrency();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getAuthMode();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getAuthResult();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getAuthNumber();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getWarrantyResult();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getPaymentCertificate();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getResult();
		
		if( $this->getHash() != null ) {
			$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
			$signature .= $this->getHash();
		}
		
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCertificat();
		
		$this->signature_processed = sha1( $signature );
		
	}
	
	/**
	 * permet de calculer la signature
	 * @return string
	 */
	public function getSignatureProcessed() {
		if( $this->signature_processed === null ) {
			$this->processSignature();
		}
		
		return $this->signature_processed;
	}
	
	public function setSignature( $value ) {
		$this->signature_received = strval($value);
	}
	
	public function getSignature() {
		return $this->signature_received;
	}
	
	public function checkSignature() {		
		return $this->getSignature() ==  $this->getSignatureProcessed();
	}
}