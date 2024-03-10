@extends('layout')
  
@section('content')
<div class="row">
    
    {{-- <form id='checkout-form' method='post' action="{{ route('order') }}">   
        @csrf   --}}
    <div class="col-6">
        <div class="panel panel-default credit-card-box">
            <div class="panel-heading display-table" >
                <h2 class="panel-title" >Payment Info</h2>
            </div>
            <div class="panel-body">
                 <form id='checkout-form' method='post' action="{{ route('order') }}">   
                    @csrf             
                    <input type='hidden' name='stripeToken' id='stripe-token-id'>                              
                    <br>
                    <div id="card-element" class="form-control" ></div>
                    {{-- <button 
                        id='pay-btn'
                        class="btn btn-success mt-3"
                        type="button"
                        style="margin-top: 20px; width: 100%;padding: 7px;"
                        onclick="createToken()">Order
                    </button> --}}
                {{-- <form> --}}
  
            </div>
        </div>
    </div>
    <div class="col-6">
        <table id="cart" class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:10%">Price</th>
                    <th style="width:8%">Quantity</th>
                    <th style="width:22%" class="text-center">Subtotal</th>
                    
                </tr>
            </thead>
            <tbody>
                @php $total = 0 @endphp
                @if(session('cart'))
                    @foreach(session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                        <tr data-id="{{ $id }}">
                            <td data-th="Product">
                                <div class="row">
                                    <div class="col-sm-3 hidden-xs"><img src="{{ $details['image'] }}" width="50" height="50" class="img-responsive"/></div>
                                    <div class="col-sm-9">
                                        <h6 class="nomargin">{{ $details['name'] }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td data-th="Price">${{ $details['price'] }}</td>
                            <td data-th="Quantity">
                                {{ $details['quantity'] }}
                                
                            </td>
                            <td data-th="Subtotal" class="text-center">${{ $details['price'] * $details['quantity'] }}</td>
                            
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><h3><strong>Total ${{ $total }}</strong></h3></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">
                        <a href="{{ url('/') }}" class="btn btn-warning"><i class="fa fa-angle-left"></i> Continue Shopping</a>
                        <button  type="button" class="btn btn-success" id='pay-btn' onclick="createToken()">Order</button>
                        {{-- <button 
                        id='pay-btn'
                        class="btn btn-success mt-3"
                        type="button"
                        style="margin-top: 20px; width: 100%;padding: 7px;"
                        onclick="createToken()">Order
                    </button> --}}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <form>
</div>

@endsection
  
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
  
    var stripe = Stripe('{{ env('STRIPE_KEY') }}')
    var elements = stripe.elements();
    var cardElement = elements.create('card');
    cardElement.mount('#card-element');
  
    /*------------------------------------------
    --------------------------------------------
    Create Token Code
    --------------------------------------------
    --------------------------------------------*/
    function createToken() {
        // document.getElementById("pay-btn").disabled = true;
        stripe.createToken(cardElement).then(function(result) {
   
            if(typeof result.error != 'undefined') {
                document.getElementById("pay-btn").disabled = false;
                alert(result.error.message);
            }
  
            /* creating token success */
            if(typeof result.token != 'undefined') {
                document.getElementById("stripe-token-id").value = result.token.id;
                document.getElementById('checkout-form').submit();
            }
        });
    }
</script>
@endsection