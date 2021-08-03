<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewBorrowerGuarantorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW public.view_loan_borrower_guarantors
        AS SELECT l.id AS id_loan,
            l.code AS code_loan,
            a.id AS id_affiliate,
            a.identity_card AS identity_card_affiliate,
            a.registration AS registration_affiliate,
            a.last_name AS last_name_affiliate,
            a.mothers_last_name AS mothers_last_name_affiliate,
            a.first_name AS first_name_affiliate,
            a.second_name AS second_name_affiliate,
            a.surname_husband AS surname_husband_affiliate,
            (((((((COALESCE(a.first_name, ''::character varying)::text || ' '::text) || COALESCE(a.second_name, ''::character varying)::text) || ' '::text) || COALESCE(a.last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.mothers_last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.surname_husband, ''::character varying)::text AS full_name_affiliate,
            ca.first_shortened AS city_exp_first_shortened_affiliate,
            ast.name AS state_type_affiliate,
            afs.name AS state_affiliate,
            pe.name AS pension_entity_affiliate,
            s.identity_card AS identity_card_borrower,
            s.registration AS registration_borrower,
            s.last_name AS last_name_borrower,
            s.mothers_last_name AS mothers_last_name_borrower,
            s.first_name AS first_name_borrower,
            s.second_name AS second_name_borrower,
            s.surname_husband AS surname_husband_borrower,
            (((((((COALESCE(s.first_name, ''::character varying)::text || ' '::text) || COALESCE(s.second_name, ''::character varying)::text) || ' '::text) || COALESCE(s.last_name, ''::character varying)::text) || ' '::text) || COALESCE(s.mothers_last_name, ''::character varying)::text) || ' '::text) || COALESCE(s.surname_husband, ''::character varying)::text AS full_name_borrower,
            cs.first_shortened AS city_exp_first_shortened_borrower,
            pm.name AS sub_modality_loan,
            pm.shortened AS shortened_sub_modality_loan,
            pt.second_name AS modality_loan,
            pt.name AS name_modality_loan,
            l.amount_approved AS amount_approved_loan,
            la.quota_treat AS quota_loan,
            ls.name AS state_loan,
            c.name AS city_loan,
            u.username AS user_loan,
            r.display_name AS name_role_loan,
            l.loan_term,
            l.disbursement_date AS disbursement_date_loan,
            l.request_date AS request_date_loan,
            l.validated AS validated_loan,
            la.type AS type_affiliate_spouse_loan,
            la.guarantor AS guarantor_loan,
            l.guarantor_amortizing AS guarantor_amortizing_loan
           FROM loans l
             JOIN loan_affiliates la ON l.id = la.loan_id
             JOIN affiliates a ON a.id = la.affiliate_id
             JOIN cities c ON l.city_id = c.id
             JOIN procedure_modalities pm ON pm.id = l.procedure_modality_id
             JOIN procedure_types pt ON pm.procedure_type_id = pt.id
             JOIN loan_states ls ON ls.id = l.state_id
             JOIN spouses s ON s.affiliate_id = a.id
             LEFT JOIN cities cs ON s.city_identity_card_id = cs.id
             LEFT JOIN cities ca ON a.city_identity_card_id = ca.id
             JOIN affiliate_states afs ON a.affiliate_state_id = afs.id
             JOIN affiliate_state_types ast ON afs.affiliate_state_type_id = ast.id
             LEFT JOIN users u ON l.user_id = u.id
             JOIN roles r ON l.role_id = r.id
             LEFT JOIN pension_entities pe ON pe.id = a.pension_entity_id
          WHERE la.type::text = 'spouses'::text
        UNION
         SELECT l.id AS id_loan,
            l.code AS code_loan,
            a.id AS id_affiliate,
            a.identity_card AS identity_card_affiliate,
            a.registration AS registration_affiliate,
            a.last_name AS last_name_affiliate,
            a.mothers_last_name AS mothers_last_name_affiliate,
            a.first_name AS first_name_affiliate,
            a.second_name AS second_name_affiliate,
            a.surname_husband AS surname_husband_affiliate,
            (((((((COALESCE(a.first_name, ''::character varying)::text || ' '::text) || COALESCE(a.second_name, ''::character varying)::text) || ' '::text) || COALESCE(a.last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.mothers_last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.surname_husband, ''::character varying)::text AS full_name_affiliate,
            ca.first_shortened AS city_exp_first_shortened_affiliate,
            ast.name AS state_type_affiliate,
            afs.name AS state_affiliate,
            pe.name AS pension_entity_affiliate,
            a.identity_card AS identity_card_borrower,
            a.registration AS registration_borrower,
            a.last_name AS last_name_borrower,
            a.mothers_last_name AS mothers_last_name_borrower,
            a.first_name AS first_name_borrower,
            a.second_name AS second_name_borrower,
            a.surname_husband AS surname_husband_borrower,
            (((((((COALESCE(a.first_name, ''::character varying)::text || ' '::text) || COALESCE(a.second_name, ''::character varying)::text) || ' '::text) || COALESCE(a.last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.mothers_last_name, ''::character varying)::text) || ' '::text) || COALESCE(a.surname_husband, ''::character varying)::text AS full_name_borrower,
            ca.first_shortened AS city_exp_first_shortened_borrower,
            pm.name AS sub_modality_loan,
            pm.shortened AS shortened_sub_modality_loan,
            pt.second_name AS modality_loan,
            pt.name AS name_modality_loan,
            l.amount_approved AS amount_approved_loan,
            la.quota_treat AS quota_loan,
            ls.name AS state_loan,
            c.name AS city_loan,
            u.username AS user_loan,
            r.display_name AS name_role_loan,
            l.loan_term,
            l.disbursement_date AS disbursement_date_loan,
            l.request_date AS request_date_loan,
            l.validated AS validated_loan,
            la.type AS type_affiliate_spouse_loan,
            la.guarantor AS guarantor_loan,
            l.guarantor_amortizing AS guarantor_amortizing_loan
           FROM loans l
             JOIN loan_affiliates la ON l.id = la.loan_id
             JOIN affiliates a ON a.id = la.affiliate_id
             JOIN cities c ON l.city_id = c.id
             JOIN procedure_modalities pm ON pm.id = l.procedure_modality_id
             JOIN procedure_types pt ON pm.procedure_type_id = pt.id
             JOIN loan_states ls ON ls.id = l.state_id
             JOIN affiliate_states afs ON a.affiliate_state_id = afs.id
             JOIN affiliate_state_types ast ON afs.affiliate_state_type_id = ast.id
             LEFT JOIN cities ca ON a.city_identity_card_id = ca.id
             LEFT JOIN users u ON l.user_id = u.id
             JOIN roles r ON l.role_id = r.id
             LEFT JOIN pension_entities pe ON pe.id = a.pension_entity_id
          WHERE la.type::text = 'affiliates'::text;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_loan_borrower_guarantors");
    }
}
