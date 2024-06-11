<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="modal-title" id="exampleModalLongTitle">Sitting</h5>
                    </div>
                    <div class="col-md-6" style="text-align:right;">
                        <button type="button" class="close" ng-click="hideModal();" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                
            </div>
            <div class="modal-body">
                <form name="myForm" novalidate="novalidate" ng-submit="onSubmit(myForm.$valid)">

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Name</label>
                            <input type="text" ng-model="formData.name" class="form-control" required />
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Mobile No.</label>
                            <input type="number" ng-model="formData.mobile_no" class="form-control" required />
                        </div>
                        <div class="col-md-4 form-group">
                            <label>PNR/UID</label>
                            <input type="text" ng-model="formData.pnr_uid" class="form-control"  />
                        </div>
                       
                      
                    </div>
                   
                    <div class="row">
                         <div class="col-md-4 form-group" ng-if="formData.id > 0">
                            <label>Check In</label>
                           
                            <input type="text" class="form-control" ng-model="formData.check_in" readonly>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>No of Adults</label>
                            <input type="number" ng-model="formData.no_of_adults" ng-change="changeAmount()" class="form-control" />
                        </div>
                        <div class="col-md-3 form-group">
                            <label>No of children</label>
                            <input type="number" ng-model="formData.no_of_children" ng-change="changeAmount()" class="form-control" />
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Baby/Staff</label>
                            <input type="number" ng-model="formData.no_of_baby_staff" class="form-control" />
                        </div>

                        <div class="col-md-3 form-group">
                            <label>Pay Type</label>
                            <select ng-model="formData.pay_type" class="form-control" required >
                                <option value="">--select--</option>
                                <option ng-repeat="item in pay_types" ng-value=@{{item.value}}>@{{ item.label}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-3 form-group">
                            <label>Hour Occ</label>
                            <select ng-model="formData.hours_occ" class="form-control" ng-change="changeAmount()" required >
                                <option value="">--select--</option>
                                <option ng-repeat="item in hours" ng-value=@{{item.value}}>@{{ item.label}}</option>
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label ng-if="entry_id !=0">Total Amount</label>
                            <label ng-if="entry_id ==0">Paid Amount</label>
                            <input type="number" ng-model="formData.total_amount" class="form-control" readonly />
                        </div>    

                        <div ng-if="entry_id !=0" class="col-md-3 form-group">
                            <label>Paid Amount</label>
                            <input type="number" ng-model="formData.paid_amount" class="form-control" readonly />
                        </div>                        
                        <div ng-if="entry_id !=0" class="col-md-3 form-group">
                            <label>Balance Amount</label>
                            <input type="number" ng-model="formData.balance_amount" class="form-control" readonly />
                        </div>
                        <div class="col-md-4 form-group" ng-if="formData.id > 0">
                            <label>Check Out</label>
                            <input type="text" class="form-control" ng-model="formData.check_out" readonly>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <textarea ng-model="formData.remarks" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary" ng-disabled="loading">
                            <span ng-if="!loading">Submit</span>
                            <span ng-if="loading">Loading...</span>
                        </button> 
                    </div>  
                    
               </form>
            </div>
           
        </div>
    </div>
</div>
