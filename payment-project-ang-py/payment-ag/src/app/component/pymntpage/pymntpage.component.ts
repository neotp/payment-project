import { Component, EventEmitter } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { PopupComponent } from "../popup/popup.component";
import { CommonModule } from '@angular/common';
import { SearchInv } from '../../interface/payment-interface';


@Component({
  selector: 'app-pymntpage',
  imports: [MatIconModule, PopupComponent, CommonModule],
  templateUrl: './pymntpage.component.html',
  styleUrl: './pymntpage.component.css'
})
export class PymntpageComponent {
  public paymentForm!: FormGroup;
  public filterData: any[] = [];
  public allData: any[] = []; 
  public customerLabel: string = '';
  public warning_pop: boolean = false;
  public payment_pop: boolean = false;
  public success_pop: boolean = false;
  public fail_pop: boolean = false;
  public warning_search: boolean = false;
  public isVisible: any;  
  public headerForm!: FormGroup;
  public searchData = {} as SearchInv;
  public loadingApp: EventEmitter<boolean> = new EventEmitter(false);

  constructor(
    private formBuilder: FormBuilder
    // , private paymentService: PaymentService
  ) {
    this.headerForm = this.formBuilder.group({
      cuscode: [{ value: null, disabled: false }]
      , invNo: [{ value: null, disabled: false }]
    });
  }

  public ngOnInit(): void {
    this.setLoading(true);
    this.loadData();
    this.setLoading(false);
  }

  private populateForm(): void {
    this.searchData.cuscode = this.headerForm.controls['cuscode'].value;
    this.searchData.invno = this.headerForm.controls['invNo'].value;
  }


  public loadData(): void {
   //
  }

  public searchPayment(): void {
    this.setLoading(true);
    this.populateForm()
    if (this.searchData.cuscode && this.searchData.invno){
      //
    } else {
      this.popup('search');
    }
    this.setLoading(false);
  }

  public createPayment(): void {
    // const { cuscode, invNo } = this.paymentForm.value;
    // this.paymentService.createPayment(cuscode, invNo).subscribe((response) => {
    //   // Handle the response and show success message or navigate
    // });
  }

  public toggleSelectAll(table: string): void {
    // Logic for handling select all checkboxes
  }

  public popup(pop: string) {
    switch (pop) {
      case 'warning':
        this.warning_pop = true;
        break;
      case 'payment':
        this.payment_pop = true;
        break;
      case 'success':
        this.success_pop = true;
        break;
      case 'fail':
        this.fail_pop = true;
        break;
      case 'search':
        this.warning_search = true;
        break;
    }
  }

  public handleConfirm(pop: string) {
    switch (pop) {
      case 'warning':
        this.warning_pop = false;
        break;
      case 'payment':
        this.payment_pop = false;
        break;
      case 'success':
        this.success_pop = false;
        break;
      case 'fail':
        this.fail_pop = false;
        break;
      case 'search':
        this.warning_search= false;
        break;
    }
  }
  public setLoading(isLoading: boolean): void {
    this.loadingApp.emit(isLoading);
  }
}
