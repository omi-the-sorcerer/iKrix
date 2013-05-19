<div class="container cpu_status">
  <table>
    <tr>
      <th>Parametro</th>
      <th colspan="2">Valor</th>
    </tr>
    <? if($temperature != ''):?>
      <tr>
        <th>Temperatura</th>
        <td><?=h($temperature)?><sup>o</sup>C</td>
        <td class="code"><?=graph_horizontal_bar($temperature, 0, 85, FALSE)?></td>
      </tr>
    <? endif?>
    <? if($speed != ''):?>
      <tr>
        <th>Velocidad</th>
        <td><?=h(si_unit($speed, $na, 1000, 0))?>Hz</td>
        <td class="code"><?=graph_horizontal_bar($speed, 200000000, 1200000000, FALSE)?></td>
      </tr>
    <? endif?>
    <? if($voltage != ''):?>
      <tr>
        <th>Voltaje</th>
        <td><?=h($voltage)?>V</td>
        <td class="code"><?=graph_horizontal_bar($voltage, $voltage < 1 ? $voltage : 1, 1.4, FALSE)?></td>
      </tr>
    <? endif?>
  </table>
</div>