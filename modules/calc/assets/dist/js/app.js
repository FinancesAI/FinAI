if (jQuery('#calc-max-credit').length) jQuery('#calc-max-credit').tooltip();

(function(){
  const calcConfig = window.calcMaxCreditConfig || {};

  function getConfigNumber(value, fallback) {
    const number = Number(value);
    return Number.isFinite(number) ? number : fallback;
  }

  function numberFormat(e,t,a,r){var s,l;return isNaN(t=Math.abs(t))&&(t=2),null==a&&(a=","),null==r&&(r="."),(l=(s=parseInt(e=(+e||0).toFixed(t))+"").length)>3?l%=3:l=0,(l?s.substr(0,l)+r:"")+s.substr(l).replace(/(\d{3})(?=\d)/g,"$1"+r)+(t?a+Math.abs(e-s).toFixed(t).replace(/-/,0).slice(2):"")}

  function init() {
    document.cmc.querySelectorAll('.js-cmc-calculate-change').forEach(input => input.addEventListener('change', function(){
      if (this.name == 'borrowers') {
        if (this.value == 1) {
          document.querySelectorAll('.js-cmc-for-borrowers[data-for-borrowers="1"]').forEach(el => el.style.display = '');
          document.querySelectorAll('.js-cmc-for-borrowers[data-for-borrowers="2"]').forEach(el => el.style.display = 'none');
        } else if (this.value == 2) {
          document.querySelectorAll('.js-cmc-for-borrowers[data-for-borrowers="1"]').forEach(el => el.style.display = 'none');
          document.querySelectorAll('.js-cmc-for-borrowers[data-for-borrowers="2"]').forEach(el => el.style.display = '');
        }
      }
      calculate();
    }));
    document.cmc.querySelectorAll('.js-cmc-calculate-input').forEach(input => {
      input.addEventListener('input', function(){
        calculate();
      });
      input.addEventListener('focusin', function(){
        this.value = this.value.replace(/[^0-9,.]/gim,'').replace(/,/g,'.');
      });
      input.addEventListener('focusout', function(){
        const v = this.value.replace(/[^0-9,.]/gim,'').replace(/,/g,'.');
        this.value = v > 0 ? numberFormat(v, 0, '.', ' ') : '';
      });
    });

    calculate();
  }

  function calculate() {
    const borrowers = document.cmc.querySelector('.cmc-radio__input[name="borrowers"]:checked').value;
    const rate = document.cmc.rate.value.replace(/[^0-9,.]/gim,'').replace(/,/g,'.');
    const monthlyIncome = document.cmc.monthlyIncome.value.replace(/[^\d;]/g,'');
    const monthLiabilities = document.cmc.monthLiabilities.value.replace(/[^\d;]/g,'');
    const totalLiabilities = 0;
    const dependants = 0;

    const term = getConfigNumber(calcConfig.term, 20);
    const calcMonthlyPayment = getConfigNumber(calcConfig.calcMonthlyPayment, 160);
    const fixedHouseholdCosts = getConfigNumber(calcConfig.fixedHouseholdCosts, 560);
    const guarantorCosts = getConfigNumber(calcConfig.guarantorCosts, 405);
    const householdMemberCosts = getConfigNumber(calcConfig.householdMemberCosts, 225);
    const pl_buffer = Object.assign({
      pl_buffer_parameter: 3,
      pl_existing_coef: 1.37,
      pl_liabilities: 20000,
      pl_off: 0,
    }, calcConfig.pl_buffer || {});
    const dsti_calculation = Object.assign({
      dsti_parameter: 50,
      dsti_interest: 6,
      dsti_coefficient: 1.49,
      dsti_liabilities: 20000,
      dsti_off: 0,
    }, calcConfig.dsti_calculation || {});
    const periodForMaxLti = getConfigNumber(calcConfig.periodForMaxLti, 72);
    const pl_buffer_parameter = getConfigNumber(calcConfig.pl_buffer_parameter, 3);
    const max_month_credit_percentage = getConfigNumber(calcConfig.max_month_credit_percentage, 40);

    const maxMonthlyPercentage = max_month_credit_percentage/100;
    const interest = rate/100;
    const period = term;

    let result;
    let minLivingCost = fixedHouseholdCosts+guarantorCosts*(borrowers-1)+householdMemberCosts*dependants;

    let dti = (maxMonthlyPercentage*monthlyIncome)-monthLiabilities;
    let pl = monthlyIncome-monthLiabilities-minLivingCost;

    let loanTerm;
    let maxPl = false;
    let maximum_loan_amount_pl;
    if (parseInt(pl_buffer.pl_off, 10) !== 1) {
      let pl_liabilities = pl_buffer.pl_liabilities;
      let pl_existing_coef = pl_buffer.pl_existing_coef;
      loanTerm = term;
      if (parseInt(totalLiabilities, 10) > parseInt(pl_liabilities, 10)) {
        pl = monthlyIncome-minLivingCost-(monthLiabilities*pl_existing_coef);
      }
      maxPl = true;
    }

    let maxLoanByLti = monthlyIncome*periodForMaxLti-totalLiabilities;
    let lti = -1*pmt(interest/12, period*12, maxLoanByLti);

    let maxMonthlyPayment = Math.round(parseFloat(Math.min(dti, pl, lti)));

    if (maxMonthlyPayment < 0) {
      result = false;
      maxMonthlyPayment = 0;
    }

    let maxLoan = pv(interest/12, period*12, maxMonthlyPayment);
    let maxCreditAmount = Math.round(parseFloat(Math.min(maxLoanByLti, maxLoan)));

    let dsti_val = false;
    let dsti_setting = dsti_calculation;
    let maxLoanDSTI;
    if (parseInt(dsti_setting.dsti_off, 10) !== 1) {
      let dsti_liabilities = dsti_setting.dsti_liabilities;
      let dsti_interest = dsti_setting.dsti_interest;
      let dsti_coef = dsti_setting.dsti_coefficient;
      let dsti_param = dsti_setting.dsti_parameter;

      loanTerm = term;

      dsti_val = dstiFormula(dsti_liabilities, totalLiabilities, monthLiabilities, monthlyIncome, dsti_interest, dsti_coef, dsti_param);

      if (dsti_val !== false) {
        maxMonthlyPayment = Math.round(parseFloat(Math.min(dti, pl, lti, dsti_val)));
        maxLoanDSTI = dsti_val/((dsti_interest/100)/12)*(1-Math.pow((1+(dsti_interest/100)/12), -loanTerm*12));
        maxCreditAmount = Math.round(parseFloat(Math.min(maxLoanByLti, maxLoan, maxLoanDSTI)));
      }
    }

    if (maxPl === true) {
      let pl_interest = pl_buffer_parameter;
      pl_interest = pl_interest/100;
      maximum_loan_amount_pl = pl/((interest+pl_interest)/12)*(1-Math.pow(1+((interest+pl_interest)/12), -(loanTerm*12)));
      maxCreditAmount = Math.round(parseFloat(Math.min(maxLoanByLti, maxLoan, maximum_loan_amount_pl)));

      if (dsti_val !== false) {
        maxCreditAmount = Math.round(parseFloat(Math.min(maxLoanByLti, maxLoan, maxLoanDSTI, maximum_loan_amount_pl)));
      }
    }

    if (maxCreditAmount < 0) {
      result = false;
      maxCreditAmount = 0;
    }

    if (maxCreditAmount <= 0) {
      maxMonthlyPayment = 0;
    }

    /*if (maxCreditAmount < loanAmount) {
      result = false;
    }*/

    if (maxMonthlyPayment < calcMonthlyPayment) {
      result = false;
    }

    let positiveLiquidity = monthlyIncome-minLivingCost-calcMonthlyPayment;
    result = positiveLiquidity > 0 && maxCreditAmount > 0;

    document.querySelector('.js-cmc-max-amount').innerText = numberFormat(maxCreditAmount, 0, '.', ' ');
    document.querySelector('.js-cmc-max-monthly-payment').innerText = maxMonthlyPayment;
  }

  function pmt(rate, nper, pv, fv, type) {
    if (!fv) {
      fv = 0;
    }
    if (!type) {
      type = 0;
    }

    if (rate === 0) {
      return -(pv+fv)/nper;
    }

    let pvif = Math.pow(1+rate, nper);
    let pmt = rate / (pvif-1)*-(pv*pvif+fv);
    if (type === 1) {
      pmt /= (1+rate);
    }

    return pmt;
  }

  function pv(rate, nper, pmt) {
    return pmt/rate*(1-Math.pow(1+rate, -nper));
  }

  function dstiFormula(dsti_liabilities, totalLiabilities, liabilities, income, interestDSTI, coefficientDSTI, dsti_parameter) {
    let dsti = false;

    if (parseInt(dsti_liabilities, 10) > parseInt(totalLiabilities, 10)) {
      dsti = (income*(dsti_parameter/100))-liabilities;
    }

    if (parseInt(dsti_liabilities, 10) <= parseInt(totalLiabilities, 10)) {
      dsti = (income*(dsti_parameter/100))-(liabilities*coefficientDSTI);
    }

    if (parseInt(dsti, 10) > 0 <= 0) {
      dsti = false;
    }

    return dsti;
  }

  document.addEventListener('DOMContentLoaded', function(){
    if (document.getElementById('calc-max-credit')) {
      init();
    }
  });
})();
