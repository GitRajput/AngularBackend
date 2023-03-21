import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Items } from '../models/Items';

@Injectable({
  providedIn: 'root',
})
export class EmployeeService {
  public apiUrl="http://localhost/Backend/";
  constructor(private _http: HttpClient) {}

  addEmployee(data: Items): Observable<Items>{
    return this._http.post<Items>(this.apiUrl+"createData", data);
  }

  updateEmployee(id: number, data: Items): Observable<Items> {
    return this._http.put<Items>(this.apiUrl+`updateData/?id=${id}`, data);
  }

  getEmployeeList(): Observable<Items[]> {
    return this._http.get<Items[]>(this.apiUrl+"getData");
  }

  deleteEmployee(id: number): Observable<any> {
    return this._http.delete(this.apiUrl+`deleteData/?id=${id}`);
  }
}
