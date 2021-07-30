<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewLoanAmortizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE or replace  VIEW public.view_loan_amortizations
        as select l.id as id_loan, l.code as code_loan, l.disbursement_date as disbursement_date_loan, lp.state_affiliate as state_affiliate_loan_payment,
              lp.id as id_loan_payment, lp.code as code_loan_payment, lp.estimated_date as estimated_date_loan_payment, lp.loan_payment_date as date_loan_payment, lp.estimated_quota as quota_loan_payment, lp.voucher as voucher_loan_payment, 
              lps.name as states_loan_payment, la.type as type_loan_payment, vt.name as voucher_type_loan_payment, v.code as code_voucher,
              a.first_name as first_name_affiliate, a.second_name as second_name_affiliate, a.last_name as last_name_affiliate, a.mothers_last_name as mothers_last_name_affiliate, a.surname_husband as surname_husband_affiliate, a.identity_card as identity_card_affiliate, a.registration as registration_affiliate,
              (coalesce(a.first_name, '') || ' ' || coalesce(a.second_name, '') || ' ' || coalesce(a.last_name, '')|| ' ' || coalesce(a.mothers_last_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_affiliate,
              a.first_name as first_name_borrower, a.second_name as second_name_borrower, a.last_name as last_name_borrower, a.mothers_last_name as mothers_last_name_borrower, a.surname_husband as surname_husband_borrower, a.identity_card as identity_card_borrower, a.registration as registration_borrower,
              (coalesce(a.first_name, '') || ' ' || coalesce(a.second_name, '') || ' ' || coalesce(a.last_name, '')|| ' ' || coalesce(a.mothers_last_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_borrower,
              as2.name as state_affiliate, ast.name as state_type_affiliate, pe.name as pension_entity_affiliate, pm.name as modality_loan_payment, pm.shortened as modality_shortened_loan_payment, ls.name as state_loan, lp.paid_by as paid_by_loan_payment, pt.name as procedure_loan_payment,
              lp.capital_payment as capital_payment, lp.penal_remaining, lp.penal_accumulated, lp.penal_payment, lp.interest_remaining, lp.interest_accumulated, lp.interest_payment, lp.previous_balance, (lp.previous_balance - lp.capital_payment) as current_balance
                from loan_payments lp
                join loans l on l.id = lp.loan_id
                join loan_states ls on ls.id = l.state_id
                join affiliates a on a.id = lp.affiliate_id
                join affiliate_states as2 on a.affiliate_state_id = as2 .id 
                join affiliate_state_types ast on as2.affiliate_state_type_id = ast.id
                left join pension_entities pe on a.pension_entity_id = pe.id 
                join loan_affiliates la on la.affiliate_id  = a.id
                join procedure_modalities pm on pm.id = lp.procedure_modality_id
                join procedure_types  pt on pt.id = pm.procedure_type_id 
                join loan_payment_states lps on lps.id = lp.state_id
                left join vouchers v on v.payable_id = lp.id
                left join voucher_types vt on vt.id = v.voucher_type_id
                where la.type = 'affiliates'
                union
                select l.id as id_loan, l.code as code_loan, l.disbursement_date as disbursement_date_loan, lp.state_affiliate as state_affiliate_loan_payment,
              lp.id as id_loan_payment, lp.code as code_loan_payment, lp.estimated_date as estimated_date_loan_payment, lp.loan_payment_date as date_loan_payment, lp.estimated_quota as quota_loan_payment, lp.voucher as voucher_loan_payment, 
              lps.name as states_loan_payment, la.type as type_loan_payment, vt.name as voucher_type_loan_payment, v.code as code_voucher,
              a.first_name as first_name_affiliate, a.second_name as second_name_affiliate, a.last_name as last_name_affiliate, a.mothers_last_name as mothers_last_name_affiliate, a.surname_husband as surname_husband_affiliate, a.identity_card as identity_card_affiliate, a.registration as registration_affiliate,
              (coalesce(a.first_name, '') || ' ' || coalesce(a.second_name, '') || ' ' || coalesce(a.last_name, '')|| ' ' || coalesce(a.mothers_last_name, '')|| ' ' || coalesce(a.surname_husband, '')) AS full_name_affiliate,
              s.first_name as first_name_borrower, s.second_name as second_name_borrower, s.last_name as last_name_borrower, s.mothers_last_name as mothers_last_name_borrower, s.surname_husband as surname_husband_borrower, s.identity_card as identity_card_borrower, s.registration as registration_borrower,
              (coalesce(s.first_name, '') || ' ' || coalesce(s.second_name, '') || ' ' || coalesce(s.last_name, '')|| ' ' || coalesce(s.mothers_last_name, '')|| ' ' || coalesce(s.surname_husband, '')) AS full_name_borrower,
              as2.name as state_affiliate, ast.name as state_type_affiliate, pe.name as pension_entity_affiliate, pm.name as modality_loan_payment, pm.shortened as modality_shortened_loan_payment, ls.name as state_loan, lp.paid_by as paid_by_loan_payment, pt.name as procedure_loan_payment,
              lp.capital_payment as capital_payment, lp.penal_remaining, lp.penal_accumulated, lp.penal_payment, lp.interest_remaining, lp.interest_accumulated, lp.interest_payment, lp.previous_balance, (lp.previous_balance - lp.capital_payment) as current_balance
                from loan_payments lp
                join loans l on l.id = lp.loan_id
                join loan_states ls on ls.id = l.state_id
                join affiliates a on a.id = lp.affiliate_id
                join affiliate_states as2 on a.affiliate_state_id = as2 .id 
                join affiliate_state_types ast on as2.affiliate_state_type_id = ast.id
                left join pension_entities pe on a.pension_entity_id = pe.id 
                join loan_affiliates la on la.affiliate_id  = a.id
                join procedure_modalities pm on pm.id = lp.procedure_modality_id
                join procedure_types  pt on pt.id = pm.procedure_type_id
                join loan_payment_states lps on lps.id = lp.state_id
                join spouses s on s.affiliate_id = a.id
                left join vouchers v on v.payable_id = lp.id
                left join voucher_types vt on vt.id = v.voucher_type_id
                where la.type = 'spouses'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_loan_amortizations");
    }
}
