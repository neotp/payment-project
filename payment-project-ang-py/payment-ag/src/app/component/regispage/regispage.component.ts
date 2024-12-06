import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, Validators, ReactiveFormsModule, FormBuilder, FormsModule  } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  imports: [ FormsModule ,ReactiveFormsModule]
  , selector: 'app-regispage'
  , templateUrl: './regispage.component.html'
  , styleUrls: ['./regispage.component.css'
    
  ]
})
export class RegispageComponent implements OnInit {
  public registerForm: FormGroup;

  constructor(
    private router: Router
    , private formBuilder: FormBuilder
  ) {
    this.registerForm = this.formBuilder.group({
      fstname: [{ value: null, disabled: false }, [Validators.required]]
      , lstname: [{ value: null, disabled: false }, [Validators.required]]
      , customercode: [{ value: null, disabled: false }, [Validators.required]]
      , customername: [{ value: null, disabled: false }, [Validators.required]]
      , username: [{ value: null, disabled: false }, [Validators.required]]
      , password: [{ value: null, disabled: false }, [Validators.required]]
      , email: [{ value: null, disabled: false }, [Validators.required]]
      , position: [{ value: null, disabled: false }, [Validators.required]]
    });}

  ngOnInit(): void {
  //
  }

  onSubmit(): void {
    if (this.registerForm.valid) {
      // Handle form submission (e.g., send data to an API)
      console.log(this.registerForm.value);
      // Redirect or show success message
      this.router.navigate(['/home']);
    }
  }
}
