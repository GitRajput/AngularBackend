import { Component, EventEmitter, Input, Output } from '@angular/core';
import { MatSlideToggleChange } from '@angular/material/slide-toggle';

@Component({
  selector: 'app-viewprofile',
  templateUrl: './viewprofile.component.html',
  styleUrls: ['./viewprofile.component.scss']
})
export class ViewprofileComponent {
  public useDefault = false;
  member: string ="Premium Items";
  @Input() viewProfile: any;
  @Output() paidMember = new EventEmitter<any>();

  sendDetail(event:boolean){
    this.viewProfile.memberType=event;
    console.log('toggle', this.viewProfile);
    this.paidMember.emit(this.viewProfile);
  }

    public toggle(event: MatSlideToggleChange) {
        console.log('toggle', event.checked);
        this.sendDetail(event.checked);
        this.useDefault = event.checked;
    }
  
}
