<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewLoanBorrowerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(" create view view_loan_borrower as
        Select l.id as id_loan, l.code as code_loan,l.num_accounting_voucher as num_accounting_voucher_loan,l.guarantor_amortizing as guarantor_amortizing_loan, a.id as id_affiliate, a.identity_card as identity_card_affiliate,ca.first_shortened as city_exp_first_shortened_affiliate, a.registration as registration_affiliate, a.last_name as last_name_affiliate, a.mothers_last_name as mothers_last_name_affiliate, a.first_name as first_name_affiliate, a.second_name as second_name_affiliate, a.surname_husband as surname_husband_affiliate,(coalesce(a.last_name, '') || ' ' || coalesce(a.mothers_last_name, '') || ' ' || coalesce(a.first_name, '')|| ' ' || coalesce(a.second_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_affiliate, ast.name as state_type_affiliate, afs.name as state_affiliate ,pe.name as pension_entity_affiliate, s.identity_card as identity_card_borrower, cs.name as city_exp_borrower,cs.first_shortened as city_exp_first_shortened_borrower, s.registration as registration_borrower, s.last_name as last_name_borrower, s.mothers_last_name as mothers_last_name_borrower, s.first_name as first_name_borrower, s.second_name as second_name_borrower, s.surname_husband as surname_husband_borrower, (coalesce(s.last_name, '') || ' ' || coalesce(s.mothers_last_name, '') || ' ' || coalesce(s.first_name, '')|| ' ' || coalesce(s.second_name, '')|| ' ' || coalesce(s.surname_husband, '')) AS full_name_borrower ,pm.name as sub_modality_loan, pm.shortened as shortened_sub_modality_loan, pt.second_name as modality_loan , pt.name as name_modality_loan, l.amount_approved as amount_approved_loan, 
        la.quota_treat as quota_loan,la.indebtedness_calculated as indebtedness_borrower, ls.name as state_loan, c.name as city_loan, u.username as user_loan, r.display_name as name_role_loan, l.loan_term as loan_term, l.disbursement_date as disbursement_date_loan, l.request_date as request_date_loan, l.validated as validated_loan, la.type as type_affiliate_spouse_loan, la.guarantor as guarantor_loan
        from loans l 
              join loan_affiliates la on l.id= la.loan_id
              join affiliates a on a.id = l.affiliate_id
              join cities c on l.city_id = c.id
              join procedure_modalities pm on pm.id = l.procedure_modality_id
              join procedure_types pt on pm.procedure_type_id = pt.id
              join loan_states ls on ls.id = l.state_id
              join spouses s on s.affiliate_id = a.id
              left join cities cs on s.city_identity_card_id = cs.id
              left join cities ca on a.city_identity_card_id = ca.id
              join affiliate_states afs on a.affiliate_state_id = afs.id
              join affiliate_state_types ast on afs.affiliate_state_type_id = ast.id
              left join users u on l.user_id = u.id
              join roles r on l.role_id = r.id
              left join pension_entities pe on pe.id = a.pension_entity_id 
     
              where la.type = 'spouses' and l.affiliate_id = la.affiliate_id
              union
          Select l.id as id_loan, l.code as code_loan, l.num_accounting_voucher as num_accounting_voucher_loan, l.guarantor_amortizing as guarantor_amortizing_loan, a.id as id_affiliate, a.identity_card as identity_card_affiliate, ca.first_shortened as city_exp_first_shortened_affiliate, a.registration as registration_affiliate, a.last_name as last_name_affiliate, a.mothers_last_name as mothers_last_name_affiliate, a.first_name as first_name_affiliate, a.second_name as second_name_affiliate, a.surname_husband as surname_husband_affiliate,(coalesce(a.last_name, '') || ' ' || coalesce(a.mothers_last_name, '') || ' ' || coalesce(a.first_name, '')|| ' ' || coalesce(a.second_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_affiliate, ast.name as state_type_affiliate, afs.name as state_affiliate ,pe.name as pension_entity_affiliate, a.identity_card as identity_card_borrower, ca.name as city_exp_borrower, ca.first_shortened as city_exp_first_shortened_borrower, a.registration as registration_borrower, a.last_name as last_name_borrower, a.mothers_last_name as mothers_last_name_borrower, a.first_name as first_name_borrower, a.second_name as second_name_borrower, a.surname_husband as surname_husband_borrower, (coalesce(a.last_name, '') || ' ' || coalesce(a.mothers_last_name, '') || ' ' || coalesce(a.first_name, '')|| ' ' || coalesce(a.second_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_borrower ,pm.name as sub_modality_loan, pm.shortened as shortened_sub_modality_loan, pt.second_name as modality_loan , pt.name as name_modality_loan, l.amount_approved as amount_approved_loan, 
          la.quota_treat as quota_loan, la.indebtedness_calculated as indebtedness_borrower, ls.name as state_loan, c.name as city_loan, u.username as user_loan, r.display_name as name_role_loan, l.loan_term as loan_term, l.disbursement_date as disbursement_date_loan, l.request_date as request_date_loan, l.validated as validated_loan, la.type as type_affiliate_spouse_loan, la.guarantor as guarantor_loan
     
              from loans l 
              join loan_affiliates la on l.id= la.loan_id
              join affiliates a on a.id = l.affiliate_id
              join cities c on l.city_id = c.id
              left join cities ca on a.city_identity_card_id = ca.id
              join procedure_modalities pm on pm.id = l.procedure_modality_id
              join procedure_types pt on pm.procedure_type_id = pt.id
              join loan_states ls on ls.id = l.state_id   
              join affiliate_states afs on a.affiliate_state_id = afs.id
              join affiliate_state_types ast on afs.affiliate_state_type_id = ast.id
              left join users u on l.user_id = u.id
              join roles r on l.role_id = r.id
              left join pension_entities pe on pe.id = a.pension_entity_id          
     
              where la.type = 'affiliates' and l.affiliate_id = la.affiliate_id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_loan_borrower");
    }
}
