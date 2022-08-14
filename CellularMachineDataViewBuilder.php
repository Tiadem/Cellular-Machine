<?php


class CellularMachineDataViewBuilder{

    private $activeView = 'sumRowValuesWithLog';

    public function __construct(
        private CellularMachinePatternTransformer $transformer,
        private CreatViewBodyStrategyInterface $strategy
    ){}

    public function setStrategy(CreatViewBodyStrategyInterface $strategy): self{
        $this->strategy = $strategy;
        return $this;
    }

    public function setActiveView(string $activeStep): self{
        $this->activeView = $activeStep;
        return $this;
    }

    private function isActiveView($currentStep): bool{
        return $this->activeView === $currentStep;
    }

    public function createNavigation(array $navigationParameters): string{

        $dataFunctionNames = array_map(
            fn(array $navigationElementParameters) =>
            $this->createNavigationElement(
                $navigationElementParameters['transformStep'],
                $navigationElementParameters['title']
            ),
            $navigationParameters
        );
        $navigationBody = implode(' ',$dataFunctionNames);

        return sprintf('
          <ul class="nav nav-pills" id="myTab" role="tablist">
            %s
          </ul>
        ',$navigationBody);
    }

    public function createNavigationElement(string $dataFunctionName,string $title): string{

        return  sprintf('    
            <li class="nav-item" role="presentation">
                <button
                        class="nav-link %3$s"
                        data-bs-toggle="tab"
                        data-bs-target="#%1$s"
                        type="button"
                        role="tab"
                        aria-controls="%1$s"
                        aria-selected="false"
                >
                    %2$s
                </button>
            </li>
            ',$dataFunctionName,$title,$this->isActiveView($dataFunctionName) ? 'active' : '');
    }

    public function createStepView(string $dataFunctionName): string{
        return sprintf('
            <div class="tab-pane fade show active" id="%1$s" role="tabpanel" aria-labelledby="%1$s">
                <table>'
                    .$this->createStepHeader()
                    .$this->strategy->createBody($this->transformer->$dataFunctionName())
            .'</table>',);
    }

    private function createStepHeader(): string{
        return '
        <tr>
            <th>Index</th>
            <th>Wartości</th>
        </tr>';
    }

}
