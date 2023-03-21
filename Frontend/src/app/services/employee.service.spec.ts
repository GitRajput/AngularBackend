import { TestBed } from '@angular/core/testing';
import { Items } from '../models/Items';
import {
  HttpClientTestingModule,
  HttpTestingController
} from '@angular/common/http/testing';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { EmployeeService } from './employee.service';

describe('EmployeeService', () => {
  let service: EmployeeService;
  let httptestoControl: HttpTestingController;
  let HttpClient: HttpClient;
  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [EmployeeService],
    });
    service = TestBed.inject(EmployeeService);
  });

  beforeEach(() => {
   
    service = TestBed.inject(EmployeeService);
    httptestoControl =TestBed.inject(HttpTestingController);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('HTTPclient get method', () => {
    const employee: Items[] =[
        {
            "id": 717,
            "name": "43",
            "zip": "0",
            "item": "LED",
            "amount": 226018,
            "quantity": 23000
        },
        {
            "id": 1221,
            "name": "21",
            "zip": "0",
            "item": "OLED",
            "amount": 210251,
            "quantity": 140000
        }
    ];
    
    service.getEmployeeList().subscribe((data)=>{
      expect(employee).toBe(data, "should check it mock data.")
    });
    const req = httptestoControl.expectOne(service.apiUrl)
    expect(req.cancelled).toBeFalsy();
    expect(req.request.responseType).toEqual('json');

    req.flush(employee);
    httptestoControl.verify();
  });

  it('should add employee and return added empolyee', () => {
    const employee: Items =
      {
            "id": 234,
            "name": "21",
            "zip": "0",
            "item": "OLED",
            "amount": 210251,
            "quantity": 140000
    }
    ;
    service.addEmployee(employee).subscribe((data)=>{
      expect(data).toBe(employee);
    });
    const req = httptestoControl.expectOne(service.apiUrl)
    expect(req.cancelled).toBeFalsy();
    expect(req.request.responseType).toEqual('json');

    req.flush(employee);
    
  });
  
  it('should test 404 error',()=>{
    const errorMsg="mock 404 error occured";
    service.getEmployeeList().subscribe((data)=>{
      fail('failing with error 404');
    },
    (error: HttpErrorResponse)=>{
      expect(error.status).toEqual(404);
      expect(error.error).toEqual(errorMsg);
    }
    );
  })

});
